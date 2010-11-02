<?php

class UserTask extends MigratorTask {

	var $_genderMap = array(
		null => null,
		0 => null,
		'UNKNOWN' => null,
		'M' => 'm',
		'F' => 'f',
	);

	var $_classificationMap = array(
		null => null,
		0 => null,
		'WEEKEND_FULLERTON' => 1,
		'WEEKEND_SOUTHCOUNTY' => 2,
		'WEEKEND_345' => 3,
		'ONLINE' => 4,
		'CURIOUS' => 5,
		'FRIEND' => 6
	);

	var $_maritalStatusMap = array(
		null => null,
		0 => null,
		'UNKNOWN' => null,
		'SINGLE' => 's',
		'MARRIED' => 'm',
		'DIVORCED' => 'd',
		'WIDOWED' => 'w'
	);

	var $_gradeMap = array(
		null => null,
		0 => null,
		'UNKNOWN' => null,
		'PREKINDER' => -1,
		'KINDER' => 0,
		'FIRST' => 1,
		'SECOND' => 2,
		'THIRD' => 3,
		'FOURTH' => 4,
		'FIFTH' => 5,
		'SIXTH' => 6,
		'SEVENTH' => 7,
		'EIGHTH' => 8,
		'NINTH' => 9,
		'TENTH' => 10,
		'ELEVENTH' => 11,
		'TWELFTH' => 12
	);

	var $_oldPkMapping = array(
		'entered_by_person_id' => array('person' => 'User'),
		'grade_school_id' => array('schools' => 'School'),
		'middle_school_id' => array('schools' => 'School'),
		'high_school_id' => array('schools' => 'School'),
		'college_id' => array('schools' => 'School'),
		'job_category_id' => array('job_categories' => 'JobCategory'),
	);

	var $_oldTable = 'person';
	var $_oldPk = 'person_id';
	var $_newModel = 'User';

	function mapData() {
		$this->User->Profile->Behaviors->detach('Logable');

		$oldData = $this->_editingRecord;
		$roles = explode(',', $oldData['roles']);
		$group = 8;
		if (in_array('RHSTAFF', $roles)) {
			$group = 5;
		}
		if (in_array('COMMADMIN', $roles)) {
			$group = 4;
		}
		if (in_array('PASTORALSTAFF', $roles)) {
			$group = 3;
		}
		if (in_array('ADMIN', $roles)) {
			$group = 2;
		}
		if (!isset($oldData['work_phone_ext'])) {
			$oldData['work_phone_ext'] = null;
		}
		if (!isset($oldData['non_migratable'])) {
			$oldData['non_migratable'] = null;
		}
		$userData = array(
			'User' => array(
				'username' => $oldData['username'],
				'password' => $oldData['password'],
				'active' => $oldData['active'],
				'created' => $oldData['created'],
				'last_logged_in' => $oldData['last_logged_in'],
				'flagged' => 0,
				'reset_password' => $oldData['reset_password'],
				'group_id' => $group
			),
			'Profile' => array(
				'first_name' => $oldData['first_name'],
				'last_name' => $oldData['last_name'],
				'gender' => $oldData['gender'],
				'birth_date' => array(
					'month' => $oldData['birth_month'],
					'day' => $oldData['birth_date'],
					'year' => $oldData['birth_year']
				),
				'adult' => $oldData['is_adult'],
				'classification_id' => $oldData['classification'],
				'marital_status' => $oldData['marital_status'],
				'job_category_id' => $oldData['job_category_id'],
				'occupation' => $oldData['occupation'],
				'accepted_christ' => $oldData['accepted_christ'],
				'accepted_christ_year' => $oldData['accepted_christ_year'],
				'baptism_date' => $oldData['baptism_date'],
				'allergies' => $oldData['allergies_description'],
				'special_needs' => $oldData['special_needs_description'],
				'special_alert' => $oldData['child_special_alert_description'],
				'cell_phone' => $oldData['cell_phone'],
				'home_phone' => $oldData['home_phone'],
				'work_phone' => $oldData['work_phone'],
				'work_phone_ext' => $oldData['work_phone_ext'],
				'primary_email' => $oldData['primary_email'],
				'alternate_email_1' => $oldData['alternate_email_1'],
				'alternate_email_2' => $oldData['alternate_email_2'],
				'cpr_certified' => $oldData['cpr_certified'],
				'baby_dedication_date' => $oldData['baby_dedication_date'],
				'qualified_leader' => $oldData['is_qualified_rh_leader'],
				'background_check_complete' => $oldData['background_check_complete'],
				'background_check_by' => $oldData['background_check_administered_by'],
				'background_check_date' => $oldData['background_check_date'],
				'grade' => $oldData['grade'],
				'graduation_year' => $oldData['high_school_grad_year'],
				'created_by' => $oldData['entered_by_person_id'],
				'created_by_type' => $oldData['entered_by_type'],
				'created' => $oldData['created'],
				'default_address' => $oldData['current_address'],
				'campus_id' => 1,
				'email_on_notification' => 0,
				'allow_sponsorage' => 0,
				'household_contact_signups' => 0,
				'elementary_school_id' => $oldData['grade_school_id'],
				'middle_school_id' => $oldData['middle_school_id'],
				'high_school_id' => $oldData['high_school_id'],
				'college_id' => $oldData['college_id'],
				'non_migratable' => serialize($oldData['non_migratable']),
			)
		);
		$this->_editingRecord = $userData;
	}

/**
 * Tries to format baptism date
 *
 * @param string $old
 * @return string
 */
	function _prepareBaptismDate($old) {
		$this->_editingRecord['non_migratable']['baptism_date'] = $old;
		if (date('Y-m-d', strtotime($old)) !== '1969-12-31') {
			list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($old)));
			$old = compact('year', 'month', 'day');
		} else {
			$old = '';
		}
		return $old;
	}

/**
 * Tries to format baby dedication date
 *
 * @param string $old
 * @return string
 */
	function _prepareBabyDedicationDate($old) {
		$this->_editingRecord['non_migratable']['baby_dedication_date'] = $old;
		if (date('Y-m-d', strtotime($old)) !== '1969-12-31') {
			list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($old)));
			$old = compact('year', 'month', 'day');
		} else {
			$old = '';
		}
		return $old;
	}

/**
 * Tries to format background check date
 *
 * @param string $old
 * @return string
 */
	function _prepareBackgroundCheckDate($old) {
		$this->_editingRecord['non_migratable']['background_check_date'] = $old;
		if (date('Y-m-d', strtotime($old)) !== '1969-12-31') {
			list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($old)));
			$old = compact('year', 'month', 'day');
		} else {
			$old = '';
		}
		return $old;
	}

/**
 * Decrypts and re-hashes the password
 *
 * @param string $old
 * @param array $oldRecord
 * @return string
 */
	function _preparePassword($old) {
		$decrypted = $this->User->decrypt($old);
		$this->_editingRecord['reset_password'] = false;
		$clean = preg_replace('/[^(\x20-\x7F)]*/','', $decrypted);
		if ($clean != $decrypted || strlen($decrypted) < 6) {
			CakeLog::write('migration', 'Invalid password, resetting: '.$decrypted);
			$decrypted = $this->User->generatePassword();
			$this->_editingRecord['reset_password'] = true;
		}
		return $decrypted;
	}


/**
 * Takes out extra gunk from phone numbers
 *
 * @param string $old
 * @param array $oldRecord
 * @return string
 */
	function _prepareCellPhone($old) {
		$old = trim($old);
		if (substr($old, 0, 1) == '1') {
			$old = substr($old, 1, -1);
		}
		$old = substr(preg_replace('/[^0-9]+/', '', $old), 0, 10);
		$Validation = new Validation();
		if (!$Validation->phone($old)) {
			$old = '';
		}
		return $old;
	}

/**
 * Takes out extra gunk from phone numbers
 *
 * @param string $old
 * @param array $oldRecord
 * @return string
 */
	function _prepareHomePhone($old) {
		return $this->_prepareCellPhone($old);
	}

/**
 * Takes out extra gunk from phone numbers and takes everything after
 * 10 digits and assumes it's an extension
 *
 * @param string $old
 * @return string
 */
	function _prepareWorkPhone($old) {
		$old = trim($old);
		if (substr($old, 0, 1) == '1') {
			$old = substr($old, 1, -1);
		}
		$phone = substr(preg_replace('/[^0-9]+/', '', $old), 0, 10);
		$ext = substr(preg_replace('/[^0-9]+/', '', $old), 11);
		if ($ext !== false) {
			$this->_editingRecord['work_phone_ext'] = $ext;
		} else {
			$this->_editingRecord['work_phone_ext'] = null;
		}
		$Validation = new Validation();
		if (!$Validation->phone($phone)) {
			$phone = '';
		}
		return $phone;
	}

/**
 * Cleans usernames
 *
 * @param string $old
 * @return string
 */
	function _prepareUsername($old) {
		if (preg_match('/[^a-z0-9_-]/i', $old) == 1 || strlen($old) < 5 || !$this->User->isUnique(array('username' => $old))) {
			CakeLog::write('migration', 'User did not validate: '.$old);
			$old = $this->User->generateUsername($this->_editingRecord['first_name'], $this->_editingRecord['last_name']);
		}
		return $old;
	}

/**
 * Clears out non-emails
 *
 * @param string $old
 * @return string
 */
	function _preparePrimaryEmail($old) {
		$Validation = new Validation();
		if (!$Validation->email($old)) {
			$old = '';
		}
		return $old;
	}
	function _prepareAlternateEmail1($old) {
		return $this->_preparePrimaryEmail($old);
	}
	function _prepareAlternateEmail2($old) {
		return $this->_preparePrimaryEmail($old);
	}

}
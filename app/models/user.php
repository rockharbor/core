<?php
/**
 * User model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * User model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class User extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'User';

/**
 * The field to use when generating lists
 *
 * @var string
 */
	var $displayField = 'username';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Logable',	
		'Containable',
		'Merge',
		'Linkable.AdvancedLinkable'
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'username' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That username is taken.'
			),
			'notempty' => array(
				'rule' => 'notEmpty',
				'message' => 'Gotta have a username.'
			)
		),
		'password' => array(			
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Your password must be at least 6 characters.'
			),
			'alphaNumeric' => array(
				'rule' => array('alphaNumeric'),
				'message' => 'Your password must contain only letters and numbers.'
			),
			'notempty' => array(
				'rule' => 'notEmpty',
				'message' => 'Gotta have a password.'
			)
		),
		'confirm_password' => array(
			'identical' => array(
				'rule' => array('identicalFieldValues', 'password'),
				'message' => 'Password confirmation must match password.'
			)
		)
	);

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'Comment' => array(
			'className' => 'Comment',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Notification' => array(
			'className' => 'Notification',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Image' => array(
			'className' => 'Image',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'User', 'Image.group' => 'Image')
		),
		'Document' => array(
			'className' => 'Document',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Document.model' => 'User', 'Document.group' => 'Document')
		),
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'foreign_key',
			'unique' => true,
			'conditions' => array('Address.model' => 'User'),
			'dependent' => true
		),
		'HouseholdMember' => array(
			'dependent' => true
		),
		'Leader' => array(
			'dependent' => true
		),
		'Payment' => array(
			'dependent' => true
		)
	);

/**
 * HasOne association link
 *
 * @var array
 */
	var $hasOne = array(
		'Profile' => array(
			'className' => 'Profile',
			'foreignKey' => 'user_id',
			'dependent' => true
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Group'
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'Publication' => array(
			'className' => 'Publication',
			'joinTable' => 'publications_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'publication_id',
			'dependent' => true
		)
	);

/**
 * Prepares a search on a user
 *
 * Returns search options unique to this model that will return a list of id's from post conditions
 * that can then be used to search paginate data
 *
 * @param object $Controller The calling controller
 * @param array $data Post data to use for conditions
 * @return array Search option array
 * @access public
 */ 
	function prepareSearch(&$Controller, $data) {
		// remove and store fields that aren't actually in the db
		$operator = $data['Search']['operator'];
		unset($data['Search']);
		$dist = $data['Distance'];
		unset($data['Distance']);
		$birthdayRange = $data['Profile']['Birthday'];
		$birthdayRange = array_map('Set::filter', $birthdayRange);
		unset($data['Profile']['Birthday']);
		$email = $data['Profile']['email'];
		unset($data['Profile']['email']);
		
		// remove blank
		$data = array_map('Set::filter', $data);		
		$link = $this->postContains($data);
		
		$conditions = $Controller->postConditions($data, 'LIKE', $operator);
		// prepare for a distance search
		if (!empty($dist['distance_from'])) {
			$coords = $this->Address->geoCoordinates($dist['distance_from']);
			$this->Address->virtualFields = array_merge($this->Address->virtualFields, array(
				'distance' => $this->Address->distance($coords['lat'], $coords['lng'])
			));
			
			// get addresses within distance requirements
			$distancedAddresses = $this->Address->find('all', array(
				'conditions' => array(
					$this->Address->getVirtualField('distance').' <= ' => (int)$dist['distance']
				)
			));
			
			$conditions[$operator]['Address.id'] = array_values(Set::extract('/Address/id', $distancedAddresses));
		}
		
		// prepare age group search
		if (isset($data['Profile']['age'])) {
			$ages = array();
			foreach ($data['Profile']['age'] as $ageGroup) {			
				$ageRange = explode('-', $ageGroup);
				$ages[$operator][] = array($this->Profile->getVirtualField('age').' BETWEEN ? AND ?' => array((int)$ageRange[0], (int)$ageRange[1]));
			}
			$conditions[$operator][] = $ages;
			unset($conditions['Profile.age']);
			unset($conditions[$operator]['Profile.age']);
		}
		
		// check for child
		if (isset($data['Profile']['child'])) {
			$conditions[$operator][$this->Profile->getVirtualField('child')] = $data['Profile']['child'];
			unset($conditions['Profile.child']);
		}
		
		// check for birthday range
		if (!empty($birthdayRange['start']) && !empty($birthdayRange['end'])) {
			krsort($birthdayRange['start']);
			krsort($birthdayRange['end']);
			$start = implode('-', $birthdayRange['start']);
			$end = implode('-', $birthdayRange['end']);
			$conditions['Profile.birth_date BETWEEN ? AND ?'] = array($start, $end);
		}
		
		// check for region
		if (!empty($data['Address']['Zipcode']['region_id'])) {
			$conditions[$operator]['Zipcode.region_id'] = $data['Address']['Zipcode']['region_id'];
			$link['Address']['Zipcode'] = array();
			unset($conditions['Address.Zipcode']);
		}
		
		// check for email 
		if (!empty($email)) {
			$conditions[$operator][] = array(
				'or' => array(
					'Profile.primary_email LIKE' => '%'.$email.'%',
					'Profile.alternate_email_1 LIKE' => '%'.$email.'%',
					'Profile.alternate_email_2 LIKE' => '%'.$email.'%'
				)
			);
		}
		
		// check for involvement
		if (!empty($data['Roster']['Involvement']['name'])) {
			$conditions[$operator]['Involvement.name LIKE'] = '%'.$data['Roster']['Involvement']['name'].'%';
			unset($conditions['Roster.Involvement']);
		}
		
		// check for ministry
		if (!empty($data['Roster']['Involvement']['Ministry']['name'])) {
			$conditions[$operator]['Ministry.name LIKE'] = '%'.$data['Roster']['Involvement']['Ministry']['name'].'%';
			//$link['Address']['Zipcode'] = array();
			unset($conditions['Roster.Involvement.Ministry']);
		}
		
		$group = 'User.id';
		
		return compact('link', 'conditions', 'group');
	}

	
/*
 * Generates a username from a name
 *
 * By default, it's $first_name.$last_name (without numbers). If that's taken, it
 * will continue appending numbers until it finds a unique username
 *
 * @param string $first_name User's first name
 * @param string $last_name User's last name
 * @return string Generated username
 */
	function generateUsername($first_name, $last_name) {
		for ($x=1; $x <= 8; $x++) {
			$username = strtolower($first_name.$last_name);
			$username = preg_replace('/[^a-z]/', '', $username);
			$username = str_pad($username, $x, strrev((string) time()), STR_PAD_RIGHT);
			$user = $this->findByUsername($username);
			if (!isset($user['User'])) {
				break;
			}
		}
		
		return $username;
	}
	
/*
 * Generates a random password
 *
 * Passwords are generated from a selection of random nouns and verbs,
 * and a 4-digit number is appended to the end. Characters that are 
 * difficult to discern (like '0', 'l', etc.) are replaced. Some other
 * characters are also replaced at random (like 4 for an 'a').
 *
 * @return string Generated password
 * @todo Add more nouns and verbs
 */
	function generatePassword() {
		$nouns = array('jesus', 'core', 'rockharbor', 'php', 'cake', 'pie');
		// 'is' is added in the middle		
		$verbs = array('awesome', 'swell', 'hilarious', 'thebest', 'socool');
		
		$noun = $nouns[array_rand($nouns, 1)];
		$verb = $verbs[array_rand($verbs, 1)];
		
		$rand_noun = '';
		$rand_verb = '';
		
		// shuffle the case around
		for($i = 0; $i < strlen($noun); $i++) {
			$rand_noun .= rand(0,1) ? strtoupper(substr($noun, $i, 1)) : strtolower(substr($noun, $i, 1));
		}
		for($i = 0; $i < strlen($verb); $i++) {
			$rand_verb .= rand(0,1) ? strtoupper(substr($verb, $i, 1)) : strtolower(substr($verb, $i, 1));			
		}
		
		$word = $rand_noun.'is'.$rand_verb;
		$rand_word = '';		
		// replace some letters that may be confusing, or for fun
		for($i = 0; $i < strlen($word); $i++) {
			$char = substr($word, $i, 1);
			if (in_array($char, array('a','A'))) {
				$rand_word .= rand(0,1) ? '4' : $char;
			} else if (in_array($char, array('e','E'))) {
				$rand_word .= rand(0,1) ? '3' : $char;
			} else if ($char == 'I') {
				// I and l's are confusing
				$rand_word .= rand(0,1) ? '1' : 'i';
			} else if ($char == 'l') {
				// I and l's are confusing
				$rand_word .= rand(0,1) ? '7' : 'L';			
			} else if (in_array($char, array('0', 'O'))) {
				// 0 and O's are confusing
				$rand_word .= 'o';
			} else {
				$rand_word .= $char;
			}
		}
		
		// append some numbers
		$num = '';
		for ($i = 0; $i < 4; $i++) {
			$num .= rand(2,9);
		}
		
		return $rand_word.$num;
	}
	
/*
 * Model::beforeSave() callback
 *
 * Used to hash the password field
 *
 * @return boolean True, to save
 * @see Cake docs
 */ 
	function beforeSave() {
		$this->hashPasswords(null, true);
		
		return true;
	}
	

/**
 * Custom password hashing function for Auth component
 *
 * Prevents hashing before validation so that password and confirm_password
 * validation works. Hashes if $enforce is true
 *
 * @param array $data Data passed
 * @param boolean $enforce Force hashing. Used to prevent Auth component from auto-hashing before validation
 * @return array Data with hashed passwords
 */	
	function hashPasswords($data, $enforce = false) {
		if (!isset($data[$this->alias]['confirm_password'])) {
			// being sent by auth
			// IMPORTANT: confirm_password ALWAYS needs to be sent to save encrypted password
			$enforce = true;
		}
		
		if (!empty($this->data)) {
			$data = $this->data;
		}
		
		if ($enforce && isset($data[$this->alias]['password']) && !empty($data[$this->alias]['password']))  {
			$data[$this->alias]['password'] = $this->encrypt($data[$this->alias]['password']);
		}
		
		$this->data = $data;
		return $data;
	}
		
	
/**
 * Encrypts a string (used in old CORE's password authentication)
 * Uses Security.encryptKey in Configure
 *
 * @param string $str String to encrypt
 * @return string Encrypted string
 */	
	function encrypt($str) {
		if (empty($str)) {
			return '';
		}
	    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);	    
		mcrypt_generic_init($td, Configure::read('Security.encryptKey'), $iv);
		$encrypted_data = mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		    
		return $encrypted_data;
	}
	
/**
 * Decrypts a string (used in old CORE's password authentication)
 * Uses Security.encryptKey in Configure
 *
 * @param string $str String to decrypt
 * @return string Decrypted string
 */	
	function decrypt($str) {
		if (empty($str)) {
			return '';
		}
		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, Configure::read('Security.encryptKey'), $iv);
		$unencrypted_data = mdecrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
					
		return trim($unencrypted_data);
	}
}
?>
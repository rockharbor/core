<?php
/**
 * Formatting helper class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.views.helpers
 */

/**
 * Formatting Helper
 *
 * Aids in keeping formatting standardized and consistent
 *
 * @package       core
 * @subpackage    core.app.views.helpers
 */
class FormattingHelper extends AppHelper {

/**
 * Additional helpers used
 *
 * @var array
 */
	var $helpers = array(
		'Html',
		'Time',
		'Number',
		'Permission'
	);

/**
 * Formats an email
 *
 * @param string $email The email address
 * @param integer $id The user's id, if any
 */
	function email($email, $id = null) {
		if (empty($email)) {
			return null;
		}
		$url = array('controller' => 'sys_emails', 'action' => 'user', 'User' => $id);
		$icon = $this->Html->tag('span', 'Email', array('class' => 'core-icon icon-email'));
		if ($id !== null && $this->Permission->check($url)) {
			return $icon.$this->Html->link($email, $url, array('rel' => 'modal-none'));
		}
		return $this->Html->tag('span', $email);
	}

/**
 * Formats an address
 *
 * @param array $address The address model data
 * @param integer $link Whether or not to make a link
 */
	function address($data, $link = true) {
		$address = $data['address_line_1'];
		if (!empty($data['address_line_2'])) {
			$address .= '<br />'.$data['address_line_2'];
		}
		$address .= '<br />'.$data['city'].', '.$data['state'].' '.$data['zip'];

		$prefix = strtolower($data['model']);
		$url = array('controller' => 'reports', 'action' => $prefix.'_map', $data['model'] => $data['foreign_key']);
		$icon = $this->Html->tag('span', 'Map', array('class' => 'core-icon icon-address'));
		if ($link && $this->Permission->check($url)) {
			return $icon.$this->Html->link($address, $url, array('rel' => 'modal-none', 'escape' => false));
		}
		return $this->Html->tag('span', $address);
	}

/**
 * Makes a recurring Date model record more readable. Supports all-day,
 * recurring, permanent, etc.
 *
 * @param object $date The Date model
 * @return string Human readable recurring date
 */
	function readableDate($date) {
		$types = array('h' => 'hour', 'd' => 'day', 'w' => 'week', 'md' => 'month', 'mw' => 'month', 'y' => 'year');
		$weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

		$readable = '';
		$startDate = date('F j, Y', strtotime($date['Date']['start_date']));
		$endDate = date('F j, Y', strtotime($date['Date']['end_date']));
		$startTime = date('g:ia', strtotime($date['Date']['start_time']));
		$endTime = date('g:ia', strtotime($date['Date']['end_time']));

		// if not recurring, return simple!
		if (!$date['Date']['recurring']) {
			if ($startDate == $endDate && !$date['Date']['all_day']) {
				if ($startTime == $endTime) {
					$readable = $startDate.' @ '.$startTime;
				} else {
					$readable = $startDate.' from '.$startTime.' to '.$endTime;
				}
			} else if ($date['Date']['all_day']) {
				if ($startDate == $endDate) {
					$readable = $startDate.' all day';
				} else {
					$readable = $startDate.' to '.$endDate;
				}
			} else {
				$readable = $startDate.' @ '.$startTime.' to '.$endDate.' @ '.$endTime;
			}

			return $readable;
		}

		$type = $types[$date['Date']['recurrance_type']];

		if ($date['Date']['frequency'] > 1) {
			$type .= 's';
		} else {
			$date['Date']['frequency'] = '';
		}

		$sfx = array('th','st','nd','rd');
		$on = '';
		if ($date['Date']['recurrance_type'] == 'w') {
			$on = $weekdays[$date['Date']['weekday']];
		} else if ($date['Date']['recurrance_type'] == 'mw') {
			$on = $date['Date']['offset'];
			$val = $on%100;
			if (($val-20)%10 > 0) {
				$on .= $sfx[($val-20)%10];
			} else if ($val < 4) {
				$on .= $sfx[$val];
			} else {
				$on .= $sfx[0];
			}
			$on = 'the '.$on.' '.$weekdays[$date['Date']['weekday']];
		} else if ($date['Date']['recurrance_type'] == 'md') {
			$on = $date['Date']['day'];
			$val = $on%100;
			if (($val-20)%10 > 0) {
				$on .= $sfx[($val-20)%10];
			} else if ($val < 4) {
				$on .= $sfx[$val];
			} else {
				$on .= $sfx[0];
			}
			$on = 'the '.$on;
		}

		if ($date['Date']['recurring']) {
			$freq = $date['Date']['frequency'].' ';
			if (empty($date['Date']['frequency'])) {
				$freq = '';
			}
			$readable = 'Every '.$freq.$type;

			if ($on != '') {
				$readable .= ' on '.$on;
			}

			if (!$date['Date']['all_day'] && $date['Date']['recurrance_type'] != 'h') {
				$readable .= ' from '.$startTime.' to '.$endTime;
			}

			if ($date['Date']['recurrance_type'] != 'y') {
				$readable .= ' starting';
			} else {
				$readable .= ' on';
			}
		}

		$readable .= ' '.$startDate;

		$fromorat = '';
		($startDate !== $endDate && $startTime !== $endTime && !$date['Date']['permanent']) ? $fromorat = 'from' : $fromorat = '@';

		if (!$date['Date']['all_day'] && (!$date['Date']['recurring'] || $date['Date']['recurrance_type'] == 'h')) {
			$readable .= ' '.$fromorat.' '.$startTime.' ';
		}

		if ($startDate != $endDate) {
			$readable .= ' until '.$endDate;
		}

		if (!$date['Date']['all_day']) {
			if ($fromorat == 'from') {
				$fromorat = ' to';
			}

			if (!$date['Date']['all_day'] && (!$date['Date']['recurring'] || $date['Date']['recurrance_type'] == 'h')) {
				$readable .= ' '.$fromorat.' '.$endTime;
			}
		} else {
			$readable .= ' all day';
		}

		return $readable;
	}

/**
 * Formats an age so it's readable
 *
 * @param float $age Age in years (can be less than one)
 * @param boolean $extended Include months in ages greater than 1
 * @return string
 */
	function age($age = 0, $extended = false) {
		$out = '';	
		$years = $months = 0;
		if ($age < 1) {
			$months = round($age*12);
			$out .= $months.' mos.';
		} else {
			$years = (int)$age;
			$months = round(($age-$years)*12);
			$out = $years.' yrs.';
			if ($extended && $months > 0) {
				$out .= ', '.$months.' mos.';
			}
		}
		
		return $out;
	}
	
	
/**
 * Formats as money
 *
 * @param $amount The amount
 * @return string
 */
	function money($amount = 0) {
		return $this->Number->currency($amount);
	}
	
/**
 * Creates flags for a specific model (i.e., inactive, private, etc.)
 *
 * Data is assumed to be sent as:
 * {{{
 * array(
 *		model => array(
 *			model.key => value
 *		),
 *		association => array(
 *			association.key => value
 *		)
 *	)
 * }}}
 *
 * @param string $model The model to create flags for
 * @param array $data The data to use to create them.
 * @return string Flag HTML
 * @access public
 */
	function flags($model = null, $data = array()) {
		if (!$model || empty($data)) {
			return null;
		}
		
		if (method_exists('FormattingHelper',"_flag$model")) {
			return $this->{'_flag'.$model}($data);
		} else {
			$message  = "FormattingHelper::flags - ";
			$message .= "Missing flagging function FormattingHelper::_flag$model.";
			trigger_error($message, E_USER_NOTICE);
			return null;
		}
	}

/**
 * Formats a phone number to output as (123) 456-7890 x1234
 *
 * @param string $var 10-digit number to format
 * @param string $ext Extension, if any
 * @return string
 * @access public
 */
	function phone($var = '', $ext = '') {
		$var = preg_replace('/[^\d]/', '', $var);
		$ext = preg_replace('/[^\d]/', '', $ext);
		if (strlen($var) >= 10) {
			$phone = preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $var);
		} else if (strlen($var) == 7) {
			$phone = preg_replace('/(\d{3})(\d{4})/', '$1-$2', $var);
		} else {
			return null;
		}
		if ($ext) {
			$phone .= ' x'.$ext;
		}
		return $phone;
	}
	
/**
 * Formats a datetime
 *
 * @param string $datetime MySQL date or datetime to format
 * @return string
 * @access public
 */	
	function datetime($datetime = '') {
		$out = $this->date($datetime);
		$time = $this->time($datetime);
		if ($time) {
			$out .= ' @ '.$time;
		}
		return $out;
	}

/**
 * Formats a date to output based on whatever data is available.
 * For example, if it has all the values it displays as 4/14/1984,
 * if it has just the year, 1984, just the month, April and so on.
 *
 * @param string $datetime MySQL date or datetime to format
 * @return string
 * @access public
 */
	function date($datetime = '') {
		// we don't want 12/31/1969
		if ($datetime == '') {
			return null;
		}
		$out = array();
		// split into date and time
		$datetime = explode(' ', $datetime);
		if (count($datetime) == 1) {
			$datetime[] = '00:00:00';
		}
		$date = $datetime[0];
		// split date into chunks
		list($year, $month, $day) = explode('-', $date);
		$day = (int)$day;
		$month = (int)$month;
		$year = (int)$year;
		// April
		if (!empty($month)) {
			$out[] = date('F', strtotime($month.'/1/2000'));
		}
		// April 14
		if (!empty($day) && !empty($month)) {
			$out[] = $day;
		}
		// April 1984; 1984
		if (!empty($year)) {
			$out[] = date('Y', strtotime('1/1/'.$year));
		}
		$out = count($out) > 0 ? implode(' ', $out) : null;
		// if we have all the info, so replace it with 4/14/1984
		if (!empty($day) && !empty($month) && !empty($year)) {
			$out = date('n/j/Y', strtotime($date));
		}
		return $out;
	}

/**
 * Formats a time
 *
 * @param string $datetime MySQL date or datetime to format
 * @return string
 * @access public
 */
	function time($datetime = '') {
		if ($datetime == '') {
			return null;
		}
		return date('g:ia', strtotime($datetime));
	}

/**
 * Creates flags for a user
 * 
 * @param array $user The user
 * @return string Flags
 * @access private
 */
	function _flagUser($user) {
		// default associated data that is needed
		$_defaults = array(
			'User' => array(
				'flagged' => false,
				'active' => true
			),
			'Profile' => array(
				'background_check_complete' => false
			)
		);
		
		// move it if it was found via containable
		foreach ($_defaults as $default => $fields) {
			if (isset($user['User'][$default])) {
				$user[$default] = $user['User'][$default];
			}
		}
		
		$output = null;
		
		$user = Set::merge($_defaults, $user);
		
		if ($this->Permission->canSeePrivate()) {
			if ($user['User']['flagged']) {
				$output .= $this->Html->tag('span', '', array(
					'class' => 'core-icon icon-flagged',
					'title' => 'Flagged User'
				));
			}
			if ($user['Profile']['background_check_complete']) {
				$output .= $this->Html->tag('span', '', array(
					'class' => 'core-icon icon-background-check',
					'title' => 'Background Check Complete'
				));
			}
		}
		
		
		if (!$user['User']['active']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'core-icon icon-inactive',
				'title' => 'Inactive User'
			));
		}
		
		return $output;
	}

/**
 * Creates flags for an involvement
 * 
 * @param array $involvement The involvement
 * @return string Flags
 * @access private
 */	
	function _flagInvolvement($involvement) {		
		// default associated data that is needed
		$_defaults = array(
			'Involvement' => array(
				'previous' => 0,
				'private' => 0,
				'active' => 1
			),
			'InvolvementType' => array(
				'name' => 'Involvement'
			)
		);
		
		// move it if it was found via containable
		foreach ($_defaults as $default => $fields) {
			if (isset($involvement['Involvement'][$default])) {
				$involvement[$default] = $involvement['Involvement'][$default];
			}
		}
		
		// merge defaults
		$involvement = Set::merge($_defaults, $involvement);
		
		$output = null;

		if ($involvement['Involvement']['previous']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'core-icon icon-passed',
				'title' => 'Previous '.$involvement['InvolvementType']['name']
			));
		}

		if (!$involvement['Involvement']['active']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'core-icon icon-inactive',
				'title' => 'Inactive '.$involvement['InvolvementType']['name']
			));
		}
		
		if ($involvement['Involvement']['private']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'core-icon icon-private',
				'title' => 'Private '.$involvement['InvolvementType']['name']
			));
		}
		
		return $output;
	}
	
/**
 * Creates flags for a ministry
 * 
 * @param array $ministry The Ministry
 * @return string Flags
 * @access private
 */	
	function _flagMinistry($ministry) {
		// if used as containable, it could be formatted differently
		if (isset($ministry['Ministry']['Group'])) {
			$ministry['Group'] = $ministry['Ministry']['Group'];
		}
		
		// default associated data that is needed
		$_defaults = array(
			'Ministry' => array(
				'private' => 0,
				'active' => 1
			),
		);
		
		// move it if it was found via containable
		foreach ($_defaults as $default => $fields) {
			if (isset($ministry['Ministry'][$default])) {
				$ministry[$default] = $ministry['Ministry'][$default];
			}
		}
			
		// merge defaults
		$ministry = Set::merge($_defaults, $ministry);
		
		$output = '';
		
		if (!$ministry['Ministry']['active']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'core-icon icon-inactive',
				'title' => 'Inactive Ministry'
			));
		}
		
		if ($ministry['Ministry']['private']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'core-icon icon-private',
				'title' => 'Private Ministry'
			));
		}
		
		return $output;
	}

}


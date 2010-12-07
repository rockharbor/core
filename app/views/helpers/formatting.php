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
		'Text',
		'Time',
		'Number'
	);

/**
 * Formats an address
 *
 * @param array $address The address model data
 * @param boolean $html Allow html (default: true)
 */
	function address($data, $html = true) {
		$address = $data['address_line_1'];
		if (!empty($data['address_line_2'])) {
			$address .= PHP_EOL.$data['address_line_2'];
		}
		$address .= PHP_EOL.$data['city'].', '.$data['state'].' '.$data['zip'];

		if ($html) {
			$address = $this->Html->tag('span', str_replace(PHP_EOL, '<br />', $address), array('class' => 'address'));
		}
		return $address;
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
				$readable = $startDate.' from '.$startTime.' to '.$endTime;
			} else if ($date['Date']['all_day']) {
				$readable = $startDate.' all day';
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
			$readable = 'Every '.$freq.$type.' ';

			if ($on != '') {
				$readable .= 'on '.$on.' ';
			}

			if (!$date['Date']['all_day'] && $date['Date']['recurrance_type'] != 'h') {
				$readable .= 'from '.$startTime.' to '.$endTime.' ';
			}

			if ($date['Date']['recurrance_type'] != 'y') {
				$readable .= 'starting ';
			} else {
				$readable .= 'on ';
			}
		}

		$readable .= $startDate.' ';

		$fromorat = '';

		($startDate == $endDate && !$date['Date']['permanent']) ? $fromorat = 'from' : $fromorat = '@';

		if (!$date['Date']['all_day'] && (!$date['Date']['recurring'] || $date['Date']['recurrance_type'] == 'h')) {
			$readable .= $fromorat.' '.$startTime.' ';
		} else if ($date['Date']['all_day']) {
			//$readable .= 'all day';
		}

		$between = ($startDate == $endDate) ? '' : 'until ';

		if (!$date['Date']['all_day']) {
			if ($fromorat == 'from') {
				$fromorat = 'to ';
			}
			$readable .= $between;

			if ($startDate != $endDate) {
				$readable .= $endDate;
			}

			if (!$date['Date']['all_day'] && (!$date['Date']['recurring'] || $date['Date']['recurrance_type'] == 'h')) {
				$readable .= ' '.$fromorat.' '.$endTime;
			}
		} else {
			$readable .= $between;
			if ($startDate != $endDate) {
				$readable .= $endDate.' ';
			}
			$readable .= 'all day';
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
 * Formats a phone number to output as (123) 456-7890
 *
 * @param string $var 10-digit number to format
 * @return string
 * @access public
 */
	function phone($var = '') {
		$var = preg_replace('/[^\d]/', '', $var);
		if (strlen($var) == 10) {
			return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $var);
		} else if (strlen($var) == 7) {
			return preg_replace('/(\d{3})(\d{4})/', '$1-$2', $var);
		} else {
			return null;
		}
		
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
		// Saturday; April 14
		if (!empty($day)) {
			$out[] = empty($month) ? date('l', strtotime('1/'.$day.'/2000')) : date('j', strtotime('1/'.$day.'/2000'));
		}
		// April 1984; 1984
		if (!empty($year)) {
			$out[] = date('Y', strtotime('1/1/'.$year));
		}
		$out = implode(' ', $out);
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
			)
		);
		
		// move it if it was found via containable
		foreach ($_defaults as $default => $fields) {
			if (isset($user['User'][$default])) {
				$user[$default] = $user['User'][$default];
			}
		}
		
		$output = '';
		
		$user = array_merge($_defaults, $user);
		
		if ($user['User']['flagged']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'flagged',
				'title' => 'Flagged User',
				'rel' => 'tooltip'
			));
		}
		
		if (!$user['User']['active']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'inactive',
				'title' => 'Inactive User',
				'rel' => 'tooltip'
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
				'passed' => 0,
				'private' => 0
			),
			'Date' => array(),
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
		$involvement = array_merge($_defaults, $involvement);
		
		$output = '';
		
		$titles = array();
		if ($involvement['Involvement']['passed']) {
			$titles[] = 'Past';
		}
		if (!$involvement['Involvement']['active']) {
			$titles[] = 'Inactive';
		}
		
		if (!$involvement['Involvement']['active'] || $involvement['Involvement']['passed']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'inactive',
				'title' => $this->Text->toList($titles).' '.$involvement['InvolvementType']['name'],
				'rel' => 'tooltip'
			));
		}
		
		if ($involvement['Involvement']['private']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'private',
				'title' => 'Private '.$involvement['InvolvementType']['name'],
				'rel' => 'tooltip'
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
				'private' => 0
			),
		);
		
		// move it if it was found via containable
		foreach ($_defaults as $default => $fields) {
			if (isset($ministry['Ministry'][$default])) {
				$ministry[$default] = $ministry['Ministry'][$default];
			}
		}
			
		// merge defaults
		$ministry = array_merge($_defaults, $ministry);
		
		$output = '';
		
		if (!$ministry['Ministry']['active']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'inactive',
				'title' => 'Inactive Ministry',
				'rel' => 'tooltip'
			));
		}
		
		if ($ministry['Ministry']['private']) {
			$output .= $this->Html->tag('span', '', array(
				'class' => 'private',
				'title' => 'Private Ministry',
				'rel' => 'tooltip'
			));
		}
		
		return $output;
	}

}

?>
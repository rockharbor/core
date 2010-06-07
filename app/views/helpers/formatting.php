<?php

/**
 * Aids in keeping formatting standardized and consistent
 *
 * @author 		Jeremy Harris <jharris@rockharbor.org>
 * @package		app
 * @subpackage	app.views.helpers
 */
class FormattingHelper extends AppHelper {
	
	var $helpers = array(
		'Html',
		'Text',
		'Time',
		'Number'
	);

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
			if ($extended) {
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
 * array(
 *	model.key => value
 *	model.association => array(
 *		association.key => value
 *		)
 *	) 
 *
 * @param string $model The model to create flags for
 * @param array $data The data to use to create them.
 * @return string Flag HTML
 * @access public
 */
	function flags($model = null, $data = array()) {
		if (!$model || empty($data)) {
			return;
		}
		
		if (method_exists('FormattingHelper',"_flag$model")) {
			return $this->{'_flag'.$model}($data);
		} else {
			$message  = "FormattingHelper::flags - ";
			$message .= "Missing flagging function FormattingHelper::_flag$model.";
			trigger_error($message, E_USER_NOTICE);
			return '';
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
		// assumes $var is 10-digits because of model validation
		return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $var);
	}
	
/**
 * Formats a datetime to output based on whatever data is available.
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
			return '';
		}
		
		$out = array();
		
		// split into date and time
		$datetime = explode(' ', $datetime);
		if (count($datetime) == 1) {
			$datetime[] = '00:00:00';
		}
		
		$time = $datetime[1];
		$date = $datetime[0];
		
		// split time into chunks
		list($hours, $minutes) = explode(':', $time);
		$hours = (int)$hours;
		$minutes = (int)$minutes;		
		
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
		
		// at 8:12pm
		if (!empty($hours) && !empty($minutes)) {
			$out .= ' @ '.date('g:ia', strtotime($date.' '.$time));
		}	
		
		return $out;				 
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
				'passed' => 0
			),
			'Date' => array(),
			'Group' => array(),
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
		
		if (!empty($involvement['Group'])) {
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
			'Group' => array()
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
		
		if (!empty($ministry['Group']['level'])) {
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
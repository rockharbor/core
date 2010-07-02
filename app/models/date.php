<?php
/**
 * Date model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Date model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Date extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Date';

/**
 * Types of recurrance
 *
 * @var array
 */
	var $recurranceTypes = array(
		'h' => 'Hourly',
		'd' => 'Daily',
		'w' => 'Weekly',
		'md' => 'Monthly on date',
		'mw' => 'Monthly on weekday',
		'y' => 'Yearly'
	);

/**
 * Virtual field definitions
 *
 * @var array
 */
	var $virtualFields = array(
		'passed' => 'IF (Date.permanent, 0, CAST(CONCAT(Date.end_date, " ", Date.end_time) AS DATETIME) < NOW())'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Model::beforeSave() callback
 *
 * Here we're just cleaning up some of the Date data so everything
 * looks good when outputted in JavaScript
 *
 * @return boolean True, to save
 * @see Cake docs
 */
	function beforeSave() {
		// '0' out all unnecessary data
		if ($this->data['Date']['all_day']) {
			$this->data['Date']['start_time'] = '00:00:00';
			$this->data['Date']['end_time'] = '23:59:00';
		}
		
		if (!$this->data['Date']['frequency']) {
			$this->data['Date']['frequency'] = 1;	
		}
		
		return true;
	}

	
/*
 * Generates a list of dates from an involvement record within a range.
 *
 * ### Range:
 * - date $start Start date
 * - date $end End date
 *
 * @param integer $involvement_id Involvement id to pull dates for
 * @param array $range Range options
 * @return array Array of dates falling into that range
 * @access public
 */
	function generateDates($involvement_id = null, $range = array()) {
		if (!$involvement_id) {
			return false;
		}
		
		// default is this month
		if (empty($range)) {
			$range = array(
				'start' => date('Y-m-d H:i', strtotime('first day')),
				'end' => date('Y-m-d H:i', strtotime('last day'))
			);
		}
		
		$this->recursive = -1;
		$dates = $this->find('all', array(
			'conditions' => array(
				'Date.involvement_id' => $involvement_id
			)
		));
	
		$recurringDates = array();
		$exemptions = array();
				
		foreach($dates as $date) {
			if ($date['Date']['exemption']) {
				$exemptions = array_merge($exemptions, $this->_generateRecurringDates($date, $range));
			} else {
				$recurringDates = array_merge($recurringDates, $this->_generateRecurringDates($date, $range));
			}
		}

		// remove exemptions
		$compareDates = function($d1, $d2) {
			return $d1['Date']['start_date'] == $d2['Date']['start_date'] ? 0 : 1;
		};
		return array_udiff($recurringDates, $exemptions, $compareDates);
	}

/**
 * Generates recurring date from a recurring date record
 *
 * ### Range:
 * - date $start Start date
 * - date $end End date
 *
 * ### Limitations:
 * - Does not account for dates that start on the range start date but not
 * within the range hours
 *
 * @param integer $masterDate The date to recur
 * @param array $range The range of recurrance
 * @return array Array of dates falling into that range
 * @access protected
 */ 
	function _generateRecurringDates($masterDate, $range) {
		$dates = array();
		
		// for progressing date via strtotime
		$frequency = array(
			'h' => 'hour',
			'd' => 'day',
			'w' => 'week',
			'md' => 'month',
			'mw' => 'month',
			'y' => 'year'		
		);

		if (!is_int($range['start'])) {
			$range['start'] = strtotime($range['start']);
			$range['end'] = strtotime($range['end']);
		}
		
		// weekdays
		$weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

		$masterDate['Date']['start'] = strtotime($masterDate['Date']['start_date'].' '.$masterDate['Date']['start_time']);
		$masterDate['Date']['end'] = strtotime($masterDate['Date']['end_date'].' '.$masterDate['Date']['end_time']);
		
		// if it's not recurring, check to see if it falls in range
		$recurring = $masterDate['Date']['recurring'];
		$permanent = $masterDate['Date']['permanent'];
		$startsAfter = $masterDate['Date']['start'] >= $range['end'];
		$endsBefore = ($masterDate['Date']['start'] <= $range['start'] && $masterDate['Date']['end'] <= $range['start']);

		if (!$recurring && !$permanent && ($startsAfter || $endsBefore)) {
			return array();
		} elseif (!$recurring) {
			unset($masterDate['Date']['start']);
			unset($masterDate['Date']['end']);
			return array($masterDate);
		}
		
		if ($masterDate['Date']['frequency'] == 0) {
			$masterDate['Date']['frequency'] = 1;
		}
		
		if ($masterDate['Date']['all_day']) {
			$masterDate['Date']['start_time'] = '00:01:00';
			$masterDate['Date']['end_time'] = '23:59:00';
		}
		
		$onDate = $masterDate['Date']['start'];

		while ($onDate < $range['end'] && ($onDate < $masterDate['Date']['end'] || $masterDate['Date']['permanent'])) {
			$date = $masterDate;

			switch ($masterDate['Date']['recurrance_type']) {
				case 'h':					
					$date['Date']['start_date'] = date('Y-m-d', $onDate);
					$date['Date']['end_date'] = $masterDate['Date']['start_date'];
					$date['Date']['start_time'] = date('H:i:s', $onDate);
					$date['Date']['end_time'] = $date['Date']['start_time'];
				break;
				case 'y':
					list($year, $month, $day) = explode('-', $masterDate['Date']['start_date']);
					$date['Date']['start_date'] = date('Y-'.$month.'-'.$day, $onDate);
					$date['Date']['end_date'] = $date['Date']['start_date'];
					// first day of the month and set the frequency to start there so we don't miss anything
					$onDate = strtotime(date('Y-1-1', $onDate));
				break;
				case 'd':
					$date['Date']['start_date'] = date('Y-m-d', $onDate);
					$date['Date']['end_date'] = $date['Date']['start_date'];
				break;
				case 'md':
					// always on this date
					$date['Date']['start_date'] = date('Y-m-'.$masterDate['Date']['day'], $onDate);
					$date['Date']['end_date'] = $date['Date']['start_date'];
				break;
				case 'mw':
					// first day of the month and set the frequency to start there so we don't miss anything
					$onDate = strtotime('first day', $onDate);
					// get week offset 
					$week = strtotime('+'.($masterDate['Date']['offset']-1).' week', $onDate);
					// always on this weekday
					$date['Date']['start_date'] = date('Y-m-d', strtotime($weekdays[$date['Date']['weekday']], $week));
					$date['Date']['end_date'] = $date['Date']['start_date'];
				break;
				case 'w':
					$date['Date']['start_date'] = date('Y-m-d', strtotime($weekdays[$date['Date']['weekday']], $onDate));
					$date['Date']['end_date'] = $date['Date']['start_date'];
				break;
			}

			$date['Date']['start'] = strtotime($date['Date']['start_date'].' '.$date['Date']['start_time']);
			$date['Date']['end'] = strtotime($date['Date']['end_date'].' '.$date['Date']['end_time']);
			
			// add if it's not past the end range
			if ($date['Date']['start'] <= $range['end'] && $date['Date']['start'] >= $range['start']) {
				unset($date['Date']['start']);
				unset($date['Date']['end']);
				$dates[] = $date;
			}
			
			// progress forward by frequency amount
			$onDate = strtotime('+'.$masterDate['Date']['frequency'].' '.$frequency[$masterDate['Date']['recurrance_type']], $onDate);
		}

		return $dates;	
	}
}
?>
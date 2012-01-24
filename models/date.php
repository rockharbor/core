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
 * ### How recurring dates work
 * 
 * Recurring dates are a single record that contains all the information needed
 * to generate a group of dates based on a recurrance pattern. For example, "the
 * third wednesday of each month." Recurring dates also have exemptions, single
 * dates that can be removed from the recurrance pattern.
 * 
 * ### Recurrance fields
 * 
 * - `permanent` A boolean specifying if the recurrance doesn't have an end date
 * - `recurrance_type` Type of recurrance, see `$recurranceTypes`
 * - `frequency` Frequency of recurrance. If it's a weekly occurance, than 2 would
 * mean every 2 weeks. For `md` and `mw` types, it refers to the month
 * - `weekday` Only applies to `w` and `mw` types. 0-index indicator of the day
 * of the week the event falls on
 * - `day` Only applies to `md` type. Indicates the actual day of the month the
 * event falls on
 * - `offset` Only applies to `mw` type. Refers to the nth week the event falls
 * on
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
		'previous' => 'IF (:ALIAS:.permanent, 0, CAST(CONCAT(:ALIAS:.end_date, " ", :ALIAS:.end_time) AS DATETIME) < NOW())'
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
 * The array map for converting a recurranceType to a comparable strtotime()
 * string
 *
 * @var array
 */
	var $_frequency = array(
		'h' => 'hour',
		'd' => 'day',
		'w' => 'week',
		'md' => 'month',
		'mw' => 'month',
		'y' => 'year'
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
		if (isset($this->data['Date']['all_day']) && $this->data['Date']['all_day']) {
			$this->data['Date']['start_time'] = '00:00:00';
			$this->data['Date']['end_time'] = '23:59:00';
		}
		
		if (isset($this->data['Date']['frequency']) && !$this->data['Date']['frequency']) {
			$this->data['Date']['frequency'] = 1;	
		}
		
		return parent::beforeSave();
	}

	
/*
 * Generates a list of dates from an involvement record within a range.
 *
 * If limit is defined, it will return a maximum of that many dates. If a limit
 * is defined without a range end, it will return the next number of dates
 * as defined by the limit.
 *
 * ### Options:
 * - date $start Start date
 * - date $end End date
 * - integer $limit The number of dates to pull
 * - boolean $single Whether to just pull single dates, or dates that span multiple days
 *
 * @param integer $involvement_id Involvement id to pull dates for
 * @param array $options Options
 * @return array Array of dates falling into that range
 * @access public
 */
	function generateDates($involvement_id = null, $options = array()) {
		if (!$involvement_id) {
			return false;
		}

		// default is this month
		$default = array(
			'start' => strtotime('first day of this month'),
			'end' => strtotime('last day of this month')
		);
		$range = array();
		$limit = null;
		if (isset($options['limit'])) {
			$limit = $options['limit'];
		}
		if (isset($options['end'])) {
			$range['end'] = $options['end'];
		} elseif (!$limit) {
			$range['end'] = $default['end'];
		}
		if (isset($options['start'])) {
			$range['start'] = $options['start'];
		} else {
			$range['start'] = $default['start'];
		}
		
		$conditions =  array(
			'Date.involvement_id' => $involvement_id
		);
		
		if (isset($options['single'])) {
			$conditions['or'] = array(
				'DATEDIFF(Date.start_date, Date.end_date)' => 0,
				'exemption' => true
			);
		}
		
		$this->recursive = -1;
		$dates = $this->find('all', array(
			'conditions' => $conditions
		));

		$recurringDates = array();
		$exemptions = array();

		// get exemptions first
		foreach($dates as $date) {
			if ($date['Date']['exemption']) {
				$exemptions = array_merge($exemptions, $this->_generateRecurringDates($date, $range));
			}
		}
		foreach($dates as $date) {
			if (!$date['Date']['exemption']) {
				$recurringDates = array_merge($recurringDates, $this->_generateRecurringDates($date, $range, $limit, $exemptions));
			}
		}

		// order by start date
		$orderDates = function($d1, $d2) {
			$d1 = strtotime($d1['Date']['start_date'].' '.$d1['Date']['start_time']);
			$d2 = strtotime($d2['Date']['start_date'].' '.$d2['Date']['start_time']);
			return $d1 < $d2 ? -1 : 1;
		};
		usort($recurringDates, $orderDates);
		return $recurringDates;
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
 * @param integer $limit The number of dates to pull. Maximum if a range end is defined
 * @param array $exemptions Array of Date exemptions
 * @return array Array of dates falling into that range
 * @access protected
 */
	function _generateRecurringDates($masterDate, $range, $limit = null, $exemptions = array()) {
		$dates = array();
		
		if (isset($range['start']) && !is_numeric($range['start'])) {
			$range['start'] = strtotime($range['start']);
		}
		if (isset($range['end']) && !is_numeric($range['end'])) {
			$range['end'] = strtotime($range['end']);
		}

		$exemptions = Set::extract('/Date/start_date', $exemptions);

		// weekdays
		$weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

		$masterDate['Date']['start'] = strtotime($masterDate['Date']['start_date'].' '.$masterDate['Date']['start_time']);
		$masterDate['Date']['end'] = strtotime($masterDate['Date']['end_date'].' '.$masterDate['Date']['end_time']);

		// if it's not recurring, check to see if it falls in range
		$recurring = $masterDate['Date']['recurring'];
		$permanent = $masterDate['Date']['permanent'];
		if (isset($range['end'])) {
			$startsAfter = $masterDate['Date']['start'] >= $range['end'];
		} else {
			$startsAfter = false;
		}
		$endsBefore = $masterDate['Date']['start'] <= $range['start'] && $masterDate['Date']['end'] <= $range['start'];

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
		$onLimit = 0;

		$limitEnd = false;
		while (!$limitEnd && ($onDate < $masterDate['Date']['end'] || $permanent)) {
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
					// get first weekday
					$first = $weekdays[$date['Date']['weekday']].' '.date('M Y', $onDate);
					// add week offset
					$week = strtotime('+'.($masterDate['Date']['offset']-1).' week', strtotime($first));
					// always on this weekday
					$date['Date']['start_date'] = date('Y-m-d', $week);
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
			if (!isset($range['end'])) {
				$withinRange = true;
			} else {
				$withinRange = $date['Date']['start'] <= $range['end'] && $date['Date']['start'] >= $range['start'];
			}
			$withinRange = $withinRange && $date['Date']['start'] >= $range['start'];
			if ($withinRange && !in_array($date['Date']['start_date'], $exemptions)) {
				unset($date['Date']['start']);
				unset($date['Date']['end']);
				$dates[] = $date;
				$dates[count($dates)-1]['Date']['original'] = $masterDate;
				$onLimit++;
			}

			// progress forward by frequency amount			
			$onDate = strtotime('+'.$masterDate['Date']['frequency'].' '.$this->_frequency[$masterDate['Date']['recurrance_type']], $onDate);

			// should we keep looping based on the limit and end range?
			if ($limit > 0 && isset($range['end'])) {
				$limitEnd = !($onDate < $range['end']) || $onLimit == $limit;
			} else if (isset($range['end'])) {
				$limitEnd = !($onDate < $range['end']);
			} else {
				$limitEnd = $onLimit == $limit;
			}
		}

		return $dates;
	}
}
?>
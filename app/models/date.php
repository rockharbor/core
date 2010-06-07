<?php
class Date extends AppModel {
	var $name = 'Date';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	var $recurranceTypes = array(
		'h' => 'Hourly',
		'd' => 'Daily',
		'w' => 'Weekly',
		'md' => 'Monthly on date',
		'mw' => 'Monthly on weekday',
		'y' => 'Yearly'
	);
	
	var $virtualFields = array(
		'passed' => 'IF (Date.permanent, 0, CAST(CONCAT(Date.end_date, " ", Date.end_time) AS DATETIME) < NOW())'
	);
	
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
 * ####Range:
 * - date $start Start date
 * - date $end End date
 *
 * @author Jeremy Harris <jharris@rockharbor.org>
 * @param integer $involvement_id Involvement id to pull dates for
 * @param array $range 
 * @return array Array of dates falling into that range
 * @access public
 */
	function generateDates($involvement_id = null, $range = array()) {
		if (!$involvement_id) {
			return;
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
				'involvement_id' => $involvement_id
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
		return array_udiff($recurringDates, $exemptions, array('self', '_compareDates'));
	}

/**
 * Compares two Date models to check excemptions
 *
 * @param date $date1 The recurring date
 * @param date $date2 The exemption
 * @return integer Returns 0 to exclude, 1 to keep
 * @access private
 */ 
	function _compareDates($date1, $date2) {	
		$datestamp1 = $date1['Date']['start_date'];
		$datestamp2 = $date2['Date']['start_date'];
		return $datestamp1 == $datestamp2 ? 0 : 1;
	}

/**
 * Generates recurring date from a recurring date record
 *
 * ####Range:
 * - date $start Start date
 * - date $end End date
 *
 * @author Jeremy Harris <jharris@rockharbor.org>
 * @param integer $date The date to recur
 * @param array $range The range of recurrance
 * @return array Array of dates falling into that range
 * @access private
 */ 
	function _generateRecurringDates($date, $range) {
		$masterDate = $date;
		
		$dates = array();
		
		// for progressing date via strtotime
		$types = array(
			'h' => 'hour',
			'd' => 'day',
			'w' => 'week',
			'md' => 'month',
			'mw' => 'month',
			'y' => 'year'		
		);
		
		// weekdays
		$weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		
		// if it's not recurring, check to see if it falls in range
		if (!$masterDate['Date']['recurring'] && $masterDate['Date']['start_date'] <= $range['start'] && $masterDate['Date']['start_date'] >= $range['end'] && !$masterDate['Date']['permanent']) {
			return array();
		} elseif (!$masterDate['Date']['recurring']) {
			return array($masterDate);
		}
		
		if ($masterDate['Date']['frequency'] == 0) {
			$masterDate['Date']['frequency'] = 1;
		}
		
		if ($masterDate['Date']['all_day']) {
			$masterDate['Date']['start_time'] = '00:01:00';
			$masterDate['Date']['end_time'] = '23:59:00';
		}
		
		$endDate = $masterDate['Date']['end_date'].' '.$masterDate['Date']['end_time'];
		$onDate = $masterDate['Date']['start_date'].' '.$masterDate['Date']['start_time'];;
			
		while (strtotime($onDate) < strtotime($range['end']) && (strtotime($onDate) < strtotime($endDate) || $masterDate['Date']['permanent']))
		{
			// start on the next date
			$curdate = $masterDate;
	
			switch ($masterDate['Date']['recurrance_type']) {
				case 'h':					
					$curdate['Date']['start_date'] = date('Y-m-d', strtotime($onDate));
					$curdate['Date']['end_date'] = $masterDate['Date']['start_date'];
					$curdate['Date']['start_time'] = date('H:i', strtotime($onDate));
					$curdate['Date']['end_time'] = $curdate['Date']['start_time'];
				break;
				case 'y':
					list($year, $month, $day) = explode('-', $masterDate['Date']['start_date']);
					$curdate['Date']['start_date'] = date('Y-'.$month.'-'.$day, strtotime($onDate));
					$curdate['Date']['end_date'] = date('Y-'.$month.'-'.$day, strtotime($onDate));
				break;
				case 'd':
					$curdate['Date']['start_date'] = date('Y-m-d', strtotime($onDate));
					$curdate['Date']['end_date'] = $masterDate['Date']['start_date'];
				break;
				case 'md':
					// always on this date
					$curdate['Date']['start_date'] = date('Y-m-'.$masterDate['Date']['day'], strtotime($onDate));
					$curdate['Date']['end_date'] = $curdate['Date']['start_date'];
				break;
				case 'mw':
					// first day of the month
					$firstDay = date('Y-m-1', strtotime($onDate));
					// get week offset 
					$week = strtotime('+'.($masterDate['Date']['offset']-1).' week', strtotime($firstDay));
					// always on this weekday
					$curdate['Date']['start_date'] = date('Y-m-d', strtotime($weekdays[$curdate['Date']['weekday']], $week));
					$curdate['Date']['end_date'] = $curdate['Date']['start_date'];
				break;
				case 'w':
					$curdate['Date']['start_date'] = date('Y-m-d', strtotime($weekdays[$curdate['Date']['weekday']], strtotime($onDate)));
					$curdate['Date']['end_date'] = $curdate['Date']['start_date'];
				break;
			}
			
			$dates[] = $curdate;	
			
			// progress forward by frequency amount
			$onDate = date('Y-m-d H:i', strtotime('+'.$masterDate['Date']['frequency'].' '.$types[$masterDate['Date']['recurrance_type']], strtotime($onDate)));
		}
		
		return $dates;	
	}
}
?>
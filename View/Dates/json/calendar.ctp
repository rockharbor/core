<?php

// format the array as a json array (/js/fullCalendar friendly)
$fcEvents = array();
$e = 0;
foreach ($events as $event) {
	foreach ($event['dates'] as $date) {
		$fcEvents[] = array(
			'id' => $event['Involvement']['id'],
			'title' => $event['Involvement']['name'],
			'allDay' => ($date['Date']['all_day']==1),
			'start' => date('Y-m-d H:i', strtotime($date['Date']['start_date'].' '.$date['Date']['start_time'])),
			'end' => date('Y-m-d H:i', strtotime($date['Date']['end_date'].' '.$date['Date']['end_time'])),
			'url' => Router::url(array(
				'controller' => 'involvements',
				'action' => 'view',
				'Involvement' => $event['Involvement']['id']
				), true
			)
		);
	}
}

$orderDates = function($d1, $d2) {
	return $d1['start'] < $d2['start'] ? -1 : 1;
};
usort($fcEvents, $orderDates);
echo $this->Js->object($fcEvents);
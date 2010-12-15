<?php
if (!isset($width)) {
	$width = 200;
}
if (!isset($filters)) {
	$filters = array();
}
if (!isset($id)) {
	$id = uniqid();
}
$url = array(
	'controller' => 'dates',
	'action' => 'calendar',
	'ext' => 'json'
);
foreach ($filters as $key => $value) {
	$url[$key] = $value;
}
$url = Router::url($url);
?>
<div id="calendar<?php echo $id; ?>" style="width:<?php echo $width; ?>px;"></div>
<?php echo $this->Html->scriptBlock(
<<<JS
		$('#calendar$id').fullCalendar({
			header: {
				left: 'prev',
				center: 'title',
				right: 'next'
			},
			events: '$url',
			eventAfterRender: function(event, element, view) {
				CORE.eventRender('calendar$id', event, element, view);
			},
			loading: function (start) {
				(start) ? CORE.eventLoading('calendar$id') : CORE.eventAfterLoad('calendar$id');
			}
		});
JS
);
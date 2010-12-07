<?php
if (!isset($width)) {
	$width = 200;
}
if (!isset($filters)) {
	$filters = array();
}

$id = uniqid();
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
			eventAfterRender: function(event, element) {
				var currentMonth = $('#calendar$id').fullCalendar('getDate').getMonth();
				var currentDate = event.start;
				var dates = [];
				while(currentDate < event.end) {
					var dayClass = currentDate.getFullYear() + '-' + (currentDate.getMonth()+1) + '-' + currentDate.getDate();
					dates.push(dayClass);
					if (currentDate.getMonth() == currentMonth) {
						$('.fc-day-number').filter(function() {
							return $(this).text().toLowerCase() == Number(currentDate.getDate());
						}).parent().addClass('event '+dayClass).data('dates', dates);
					}					
					currentDate = new Date(currentDate.getTime() + 86400000);
					element.addClass(dayClass);
				}
			},
			loading: function (start) {
				if (start) {
					CORE.removeEventTooltips('calendar$id');
				} else {
					CORE.createEventTooltips('calendar$id');
				}
			}
		});
JS
);



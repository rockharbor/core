<?php
if (!isset($filters)) {
	$filters = array();
}
if (!isset($size)) {
	$size = 'mini';
}
if ($size == 'mini' && !isset($width)) {
	$width = '200px';
} elseif (!isset($width)) {
	$width = '100%';
}
if (!isset($id)) {
	$id = uniqid();
}
$url = array(
	'controller' => 'dates',
	'action' => 'calendar',
	'ext' => 'json',
	$size
);
foreach ($filters as $key => $value) {
	$url[$key] = $value;
}
$url = Router::url($url);
?>
<div id="calendar<?php echo $id; ?>" style="width:<?php echo $width; ?>;" class="<?php echo $size; ?>-calendar"></div>
<?php
$options = "
header: {
	left: 'prev',
	center: 'title',
	right: 'next'
},
events: '$url'";

if ($size == 'mini') {
	$options .= ",
	eventAfterRender: function(event, element, view) {
		CORE.eventRender('calendar$id', event, element, view);
	},
	loading: function (start) {
		(start) ? CORE.eventLoading('calendar$id') : CORE.eventAfterLoad('calendar$id');
	}";
}

echo $this->Js->buffer("$('#calendar$id').fullCalendar({{$options}});");
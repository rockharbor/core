<?php

echo $this->Html->css('fullcalendar/main', array(), array('inline' => false));
echo $this->Html->css('fullcalendar/grid', array(), array('inline' => false));
echo $this->Html->css('fullcalendar/agenda', array(), array('inline' => false));

echo $this->Html->script('fullcalendar/main', array('inline' => false));
echo $this->Html->script('fullcalendar/grid', array('inline' => false));
echo $this->Html->script('fullcalendar/agenda', array('inline' => false));
echo $this->Html->script('fullcalendar/view', array('inline' => false));
echo $this->Html->script('fullcalendar/util', array('inline' => false));

?>
<div id="calendar" style="width:700px;height:700px"></div>
<script type="text/javascript">		
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#calendar').fullCalendar({
			theme: true,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,basicWeek,agendaDay,basicDay'
			},
			events: '<?php 
			$url = array(
				'controller' => 'dates',
				'action' => 'calendar',
				'ext' => 'json'
			);
			foreach ($filters as $key => $value) {
				$url[$key] = $value;
			}
			
			echo Router::url($url);			
			?>'
		});
</script>



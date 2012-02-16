<h1>Export List</h1>
<div class="content-box">
<?php
echo $this->Form->create('Report', array(
	'url' => array(
		$model,
		$uid
	)
));
?>
<fieldset class="grid_10">
	<legend>Report options</legend>	
<?php
echo $this->Form->input('Export.type', array(
	'type' => 'select',
	'options' => array(
		'csv' => 'CSV',
		'print' => 'Print'
	),
	'value' => 'csv'
));
?>
</fieldset>
<?php
echo $this->element('report'.DS.strtolower($model).'_export_options');
echo $this->Report->end('Export');
echo $this->Form->submit('Download');
echo $this->Form->end();
?>
</div>

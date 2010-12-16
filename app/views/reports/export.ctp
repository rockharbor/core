<h2>Export List</h2>
<?php
echo $this->Form->create('Report', array(
	'url' => array(
		$model,
		$uid
	)
));

echo $this->Form->input('Export.type', array(
	'type' => 'select',
	'options' => array(
		'csv' => 'CSV',
		'print' => 'Print'
		
	),
	'value' => 'csv'
));

?>
<fieldset>
	<legend>Export fields</legend>
<?php
echo $this->element('report'.DS.strtolower($model).'_export_options');
?>
</fieldset>

<?php
echo $this->Form->submit('Download');
echo $this->Form->end();

?>
<div class="simple_lists">
<h2>Add <?php echo Inflector::humanize($modelKey); ?></h2>
<?php echo $this->Form->create($model, array('default'=>false));?>
	<fieldset>
 		<legend>Add <?php echo Inflector::humanize($modelKey); ?></legend>
	<?php
		foreach ($schema as $field => $attrs) {
			// ignore certain fields
			if (!in_array($field, array('id', 'created', 'modified'))) {
				echo $this->Form->input($field);
			}
		}
	?>
	</fieldset>
<?php 
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Add', $defaultSubmitOptions);
echo $this->Form->end();
?>
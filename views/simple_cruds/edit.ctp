<div class="simple_lists">
<h2>Edit <?php echo Inflector::humanize($modelKey); ?></h2>
<?php echo $this->Form->create($model, array('default'=>false));?>
	<fieldset>
 		<legend>Edit <?php echo Inflector::humanize($modelKey); ?></legend>
	<?php
		foreach ($schema as $field => $attrs) {
			// ignore certain fields
			if (!in_array($field, array('created', 'modified'))) {
				echo $this->Form->input($field);
			}
		}
	?>
	</fieldset>
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>


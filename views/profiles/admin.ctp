<h1>Administration</h1>
<div class="profiles">
<?php
echo $this->Form->create(array(
	'default' => false,
	'url' => $this->passedArgs,
	'inputDefaults' => array(
		'minYear' => 1900,
		'maxYear' => date('Y')
	)
));
?>
	<div>
		<fieldset class="grid_5 alpha">
			<legend>Background</legend>
			<?php
			echo $this->Form->input('id');
			echo $this->Form->input('User.flagged');
			echo $this->Form->input('cpr_certified_date');
			echo $this->Form->input('background_check_complete');
			echo $this->Form->input('background_check_by');
			echo $this->Form->input('background_check_date', array(
				'empty' => true
			));
			?>
		</fieldset>
		<fieldset class="grid_5 omega">
			<legend>User Permissions</legend>
			<?php
			echo $this->Form->input('User.active');
			echo $this->Form->input('adult');
			echo $this->Form->input('qualified_leader');
			echo $this->Form->input('User.id');
			echo $this->Form->input('User.group_id');
			echo $this->Form->input('created_by');
			echo $this->Form->input('created_by_type');
			?>
		</fieldset>
	</div>
	<div style="clear:both">
	<?php
	echo $this->Js->submit('Save', $defaultSubmitOptions);
	echo $this->Form->end();
	?>
	</div>
</div>
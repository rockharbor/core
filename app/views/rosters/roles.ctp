<div class="grid_5 alpha">
<?php
if ($this->Permission->check(array(
	'controller' => 'roles',
	'action' => 'add',
	'Ministry' => $ministry['Ministry']['id']
))) {
	echo $this->requestAction('/roles/add', array(
		'return',
		'respondAs' => 'ajax',
		'named' => array(
			'Ministry' => $ministry['Ministry']['id']
		),
		'data' => null,
		'form' => array('data' => null)
	));
}
?>
</div>
<div class="grid_5 omega">
	<fieldset>
		<legend>Add to roles</legend>
	<?php
	echo $this->Form->create('Roster', array(
		'class' => 'core-filter-form',
		'url' => $this->passedArgs,
	));
	echo $this->Form->hidden('id');
	echo $this->Form->hidden('roster_status');
	echo $this->Form->input('Role', array(
		'div' => array(
			'tag' => 'span',
			'class' => 'toggle'
		),
		'multiple' => 'checkbox',
		'label' => false
	));
	echo $this->Js->submit('Save');
	?>
	</fieldset>
	<?php
	echo $this->Form->end();
	?>
</div>
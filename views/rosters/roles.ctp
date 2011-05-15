<?php
if ($this->Permission->check(array(
	'controller' => 'roles',
	'action' => 'add',
	'Ministry' => $ministry['Ministry']['id']
))) :
	?>
<div id="addroles" class="grid_5 alpha">
<?php
	echo $this->requestAction('/roles/add/Ministry:'.$ministry['Ministry']['id'], array(
		'return',
		'renderAs' => 'ajax',
		'named' => array(
			'Ministry' => $ministry['Ministry']['id']
		),
		'data' => null,
		'form' => array('data' => null)
	));
?>
</div>
<?php endif; ?>
<div class="grid_5 omega">
<?php
echo $this->Form->create('Roster', array(
	'class' => 'core-filter-form',
	'url' => $this->passedArgs,
));
?>
	<fieldset>
		<legend>Add to roles</legend>
	<?php
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
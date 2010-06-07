<div class="ministries form">

<?php
if (!empty($revision)) {
	$changes = array_diff_assoc($revision, $this->data['Ministry']);
}
?>

<?php if ($revision && !empty($changes)): ?>
<div id="change" class="message change">
	<?php 
	echo $this->Html->link('There is a pending change for this ministry.', 
		array(
			'action' => 'history',
			'Ministry' => $this->data['Ministry']['id']
		),
		array(
			'rel' => 'modal-content'
		)
	);
	?>
</div>
<?php endif; ?>

<?php echo $this->Form->create('Ministry', array(
	'url' => array(
		'Ministry' => $this->data['Ministry']['id']
	)
));?>
	<div id="image"></div>
	<div id="image_upload">
	<?php
		// register this div as 'updateable'
		$this->Js->buffer('CORE.register(\'ImageAttachments\', \'image_upload\', \''.Router::url(array(
			'controller' => 'ministry_images',
			'action' => 'index',
			'Ministry' => $this->data['Ministry']['id']
		)).'\')');
		// and tell it to update image as well
		$this->Js->buffer('CORE.register(\'ImageAttachments\', \'image\', \''.Router::url(array(
			'controller' => 'ministry_images',
			'action' => 'view',
			'Ministry' => $this->data['Ministry']['id'],
			0, // just pull the first image
			'l'
		)).'\')');

		$this->Js->buffer('CORE.update(\'ImageAttachments\');');
	?></div>
	
	<div id="leaders">
	<?php
		// register this div as 'updateable'
		$this->Js->buffer('CORE.register("leaders", "leaders", "'.Router::url(array(
			'controller' => 'ministry_leaders',
			'Ministry' => $this->data['Ministry']['id']
		)).'")');
		
		$this->Js->buffer('CORE.update("leaders");');		
	?>
	</div>
	
	<fieldset>
 		<legend><?php printf(__('Edit %s', true), __('Ministry', true)); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('parent_id', array(
			'options' => $ministries,
			'escape' => false,
			'empty' => true,
			'label' => 'Parent Ministry'
		));
		echo $this->Form->input('campus_id');
		echo $this->Form->input('group_id', array(
			'label' => 'Private for everyone below:',
			'empty' => true
		)); 
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php 
echo $this->Form->end('Save');
?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Ministries', true)), array('action' => 'index'));?></li>
	</ul>
</div>

<?php
echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg(\'MinistryDescription\');');
?>
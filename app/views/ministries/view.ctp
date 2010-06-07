<div class="menu">
<?php 
echo $this->element('menu'.DS.'ministry', array(
	'ministries' => $ministryMenu
)); 
?>
</div>
<div class="ministries view">
<h2><?php  __('Ministry');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['parent_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['rght']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Campus Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Campus']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Group'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Group']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['modified']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ministry['Ministry']['active']; ?>
			&nbsp;
		</dd>
	</dl>


	<div class="involvements">
	<h3>Involvement opportunities</h3>
		<ul>
			<?php foreach ($ministry['Involvement'] as $involvement): ?>
			<li><?php echo $involvement['InvolvementType']['name'].': '.$this->Html->link($involvement['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['id'])); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>

</div>

<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Add Involvement Opportunity', array('controller' => 'involvements', 'action' => 'add', 'Ministry' => $ministry['Ministry']['id'])); ?> </li>
		<li><?php echo $this->Html->link('Edit Ministry', array('action' => 'edit', 'Ministry' => $ministry['Ministry']['id']), array('rel' => 'modal-content')); ?> </li>
		<li><?php echo $this->Html->link('Delete Ministry', array('action' => 'delete', $ministry['Ministry']['id']), array('id' => 'delete_btn')); ?> </li>
	</ul>
</div>

<?php
$this->Js->buffer('CORE.confirmation("delete_btn", "Are you sure you want to delete this ministry and all it\'s related content?", {update:"content"});');
?>
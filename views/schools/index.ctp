<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Schools</h1>
<?php
echo $this->Form->create(array(
	'default' => false,
	'class' => 'core-filter-form update-parent'
));
echo '<span class="toggleset">';
echo $this->Form->radio('type',
	$types,
	array(
		'legend' => false,
	)
);
echo '</span>';
echo $this->Form->end('Filter');
?>
<div class="regions index">
	<table class="datatable">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<?php
	$i = 0;
	foreach ($schools as $school):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' altrow';
		}
	?>
	<tr class="core-iconable<?php echo $class;?>">
		<td><?php echo $school['School']['name']; ?></td>
		<td><?php echo $types[$school['School']['type']]; ?></td>
		<td><span class="core-icon-container"><?php
			$icon = $this->element('icon', array('icon' => 'edit'));
			echo $this->Html->link($icon, array('action' => 'edit', $school['School']['id']), array('rel' => 'modal-parent', 'escape' => false, 'class' => 'no-hover'));
			$icon = $this->element('icon', array('icon' => 'delete'));
			echo $this->Html->link($icon, array('action' => 'delete', $school['School']['id']), array('id' => 'delete-school-'.$school['School']['id'], 'escape' => false, 'class' => 'no-hover'));
			$this->Js->buffer('CORE.confirmation("delete-school-'.$school['School']['id'].'", "Are you sure you want to delete this school?", {update:"parent"})');
		?></span></td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php echo $this->element('pagination'); ?>
<?php echo $this->Html->link('New School', array('action' => 'add'), array('rel' => 'modal-parent', 'class' => 'button')); 
<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Roles</h1>
<div class="clearfix">
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th><?php echo $this->Paginator->sort('name'); ?></th>
				<th><?php echo $this->Paginator->sort('description'); ?></th>
				<th><?php echo $this->Paginator->sort('created'); ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($roles as $role):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' altrow';
				}
			?>
			<tr class="core-iconable<?php echo $class;?>">
				<td><?php echo $role['Role']['name']; ?>&nbsp;</td>
				<td><?php echo $role['Role']['description']; ?>&nbsp;</td>
				<td><?php echo $this->Formatting->datetime($role['Role']['created']); ?>&nbsp;</td>
				<td>
					<div class="core-icon-container">
						<?php 
						echo $this->Html->link($this->element('icon', array('icon' => 'edit')), array('action' => 'edit', $role['Role']['id'], 'Ministry' => $role['Role']['ministry_id']), array('rel' => 'modal-roles', 'escape' => false, 'class' => 'no-hover'));
						echo $this->Html->link($this->element('icon', array('icon' => 'delete')), array('action' => 'delete', $role['Role']['id'], 'Ministry' => $role['Role']['ministry_id']), array('id' => 'delete_btn_'.$role['Role']['id'], 'escape' => false, 'class' => 'no-hover'));
						$this->Js->buffer('CORE.confirmation("delete_btn_'.$role['Role']['id'].'","Are you sure you want to delete this role?", {update:true});');
						?>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	echo $this->element('pagination'); ?>
</div>

<ul class="core-admin-tabs">
<?php
	$link = $this->Permission->link('Add Role', array('action' => 'add', 'Ministry' => $ministry_id), array ('rel' => 'modal-roles'));
	if ($link) {
		echo $this->Html->tag('li', $link);
	}
?>
</ul>
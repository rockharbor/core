<h1><?php __('Campuses');?></h1>
<div class="campuses index content-box">
	<table cellpadding="0" cellspacing="0" class="datatable">
		<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th>Leaders</th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
		</tr>
		<?php
		$i = 0;
		foreach ($campuses as $campus):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' altrow';
			}
		?>
		<tr class="core-iconable<?php echo $class;?>">
			<td><?php echo $this->Html->link($campus['Campus']['name'], array('view', 'Campus' => $campus['Campus']['id'])); ?>&nbsp;</td>
			<td><?php echo $campus['Campus']['description']; ?>&nbsp;</td>
			<td><?php
				$link = array('controller' => 'campus_leaders', 'action' => 'index', 'Campus' => $campus['Campus']['id']);
				$icon = $this->element('icon', array('icon' => 'add'));
				echo $this->Html->link($icon, $link, array('rel' => 'modal-parent', 'escape' => false, 'class' => 'no-hover'));
				echo $this->Html->link(count($campus['Leader']).' Leaders', $link, array('rel' => 'modal-parent'));
			?></td>
			<td><?php echo $this->SelectOptions->boolean($campus['Campus']['active']); ?>&nbsp;</td>
			<td><?php echo $this->Formatting->datetime($campus['Campus']['modified']); ?>&nbsp;</td>
			<td>
				<span class="core-icon-container">
					<?php 
					$icon = $this->element('icon', array('icon' => 'edit'));
					echo $this->Permission->link($icon, array('action' => 'edit', 'Campus' => $campus['Campus']['id']), array('rel' => 'modal-parent', 'class' => 'no-hover', 'escape' => false));
					?>
				</span>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php echo $this->element('pagination'); ?>
<?php echo $this->Permission->link('New Campus', array('action' => 'add'), array('rel' => 'modal-parent', 'class' => 'button')); ?>
</div>
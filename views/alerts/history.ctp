<?php
echo $this->MultiSelect->create();
?>
<h1>Alerts</h1>
<div class="alerts">
	<p>
		Filter:
		<?php
			echo $this->Html->link('All', array(), array('data-core-ajax' => 'true', 'class' => 'button'));
			echo $this->Html->link('Unread', array('unread'), array('data-core-ajax' => 'true', 'class' => 'button'));
			echo $this->Html->link('Read', array('read'), array('data-core-ajax' => 'true', 'class' => 'button'));
		?>
	</p>
	
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<?php
			$links = array();
			$links[] =array(
				'title' => 'Mark as read',
				'url' => array(
					'controller' => 'alerts',
					'action' => 'read'
				),
				'options' => array(
					'data-core-ajax' => 'true'
				)
			);
			echo $this->element('multiselect', array(
				'colCount' => 4,
				'checkAll' => true,
				'links' => $links
			));
			?>
		</thead>
		<tbody>
	<?php
		$i = 0;
		foreach ($alerts as $alert):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
	?>		
		<tr<?php echo $class;?>>
			<td><?php echo $this->MultiSelect->checkbox($alert['Alert']['id']); ?></td>
			<td><?php 
			$class = 'notification ' . (in_array($alert['Alert']['id'], $read) ? 'read' : 'unread');
			$style = 'padding-left:5px';
			echo $this->Html->link($alert['Alert']['name'], array('controller' => 'alerts', 'action' => 'view', $alert['Alert']['id']), array('data-core-modal' => 'true', 'class' => $class, 'style' => $style));
			?></td>
			<td><?php echo $this->Formatting->date($alert['Alert']['created']);?></td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	<?php 
	echo $this->MultiSelect->end();
	echo $this->element('pagination'); 
	?>
</div>
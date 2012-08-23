<?php
echo $this->MultiSelect->create();
?>
<h1>Alerts</h1>
<div class="alerts">
	<p>
		Filter:
		<?php
			echo $this->Js->link('All', array(), array('update' => '#content', 'class' => 'button'));
			echo $this->Js->link('Unread', array('unread'), array('update' => '#content', 'class' => 'button'));
			echo $this->Js->link('Read', array('read'), array('update' => '#content', 'class' => 'button'));
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
					'action' => 'read',
					$this->MultiSelect->token
				),
				'options' => array(
					'update' => '#content'
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
<h1>Request Types</h1>

<div class="core-tabs">
	<div class="content-box clearfix">	
		<table class="datatable">
			<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('description'); ?></th>
					<th>Notify Users</th>
					<th><?php echo $this->Paginator->sort('Modified', 'modified'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$i = 0;
				foreach ($results as $result):
					$class = null;
					if ($i++ % 2 == 0) {
						$class = ' altrow';
					}
				?>
				<tr class="<?php echo $class;?>">
					<td><?php echo $result['RequestType']['name']; ?></td>
					<td><?php echo $result['RequestType']['description']; ?></td>
					<td><?php
					$link = array('controller' => 'request_notifiers', 'action' => 'index', 'RequestType' => $result['RequestType']['id']);
					$icon = $this->element('icon', array('icon' => 'add'));
					$users = Set::extract('/RequestNotifier/User/Profile/name', $result);
					echo $this->Html->link($icon, $link, array('rel' => 'modal-requesttypes', 'escape' => false, 'class' => 'no-hover'));
					echo $this->Html->link(count($users).' Users', $link, array('rel' => 'modal-requesttypes'));
					if (!empty($users)) {
						echo $this->Html->tag('div', $this->Text->toList($users), array('class' => 'core-tooltip'));
					}
					?></td>
					<td><?php echo $this->Formatting->datetime($result['RequestType']['modified']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->element('pagination'); ?>
		<?php echo $this->Html->link('New Request Type', array('action' => 'add'), array('rel' => 'modal-parent', 'class' => 'button')); ?>
	</div>
</div>
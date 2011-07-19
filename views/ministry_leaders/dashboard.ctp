<h1>Ministry Manager Dashboard</h1>
<div class="content-box">
	<p>Manage your ministries.</p>
<?php
	echo $this->MultiSelect->create();
?>
	<table cellpadding="0" cellspacing="0"class="datatable">
		<thead>
			<?php
			$links = array(
				array(
					'title' => 'Email Ministry Managers',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'compose',
						$this->MultiSelect->token,
						'model' => 'Ministry',
						'submodel' => 'Leader'
					),
					'options' => array(
						'rel' => 'modal-none'
					),
					'permission' => true
				),
				array(
					'title' => 'Bulk Edit',
					'url' => array(
						'controller' => 'ministries',
						'action' => 'bulk_edit',
						$this->MultiSelect->token,
					),
					'options' => array(
						'rel' => 'modal-none'
					),
					'permission' => true
				)
			);
			$colCount = 3;
			echo $this->element('multiselect', array(
				'colCount' => $colCount,
				'checkAll' => true,
				'links' => $links
			));
			?>
			<tr>
				<th>&nbsp;</th>
				<th><?php echo $this->Paginator->sort('Name', 'Ministry.name');?></th>
				<th>Roles</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($leaders as $leader):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
			?>
			<tr<?php echo $class;?>>
				<td><?php echo $this->MultiSelect->checkbox($leader['Ministry']['id']); ?></td>
				<td><?php 
				echo $this->Html->link($leader['Ministry']['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $leader['Ministry']['Campus']['id']), array('escape' => false));
				if (!empty($leader['Ministry']['ParentMinistry']['id'])) {
					echo ' > ';
					echo $this->Html->link($leader['Ministry']['ParentMinistry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $leader['Ministry']['ParentMinistry']['id']), array('escape' => false));
				}
				echo ' > ';
				echo $this->Html->link($leader['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $leader['Ministry']['id'])).$this->Formatting->flags('Ministry', $leader); 
				?></td>
				<td><?php
				$link = array('controller' => 'roles', 'action' => 'index', 'Ministry' => $leader['Ministry']['id']);
				$icon = $this->element('icon', array('icon' => 'add'));
				echo $this->Html->link($icon, $link, array('rel' => 'modal', 'escape' => false, 'class' => 'no-hover'));
				echo $this->Html->link(count($leader['Ministry']['Role']).' Roles', $link, array('rel' => 'modal-parent'));
				if (!empty($leader['Ministry']['Role'])) {
					echo $this->Html->tag('div', $this->Text->toList(Set::extract('/name', $leader['Ministry']['Role'])), array('class' => 'core-tooltip'));
				}
				?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
					<?php echo $this->element('pagination'); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<?php
$this->MultiSelect->end();
?>
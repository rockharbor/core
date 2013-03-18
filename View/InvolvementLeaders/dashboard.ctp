<h1>Leader Dashboard</h1>
<div class="content-box">
	<p>Manage the involvement opportunities that you are leading.</p>
	<?php
		echo $this->Form->create('Filter', array(
			'class' => 'core-filter-form',
			'url' => $this->here,
		));
		echo $this->Form->input('previous', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('inactive', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('private', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('affiliated', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Js->submit('Filter');
		echo $this->Form->end();
	?>
<?php
	echo $this->MultiSelect->create();
?>
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<?php
			$links = array(
				array(
					'title' => 'Email Users',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'involvement',
						'users'
					),
					'options' => array(
						'data-core-modal' => '{"update":false}'
					),
					'permission' => true
				),
				array(
					'title' => 'Email Leaders',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'involvement',
						'leaders'
					),
					'options' => array(
						'data-core-modal' => '{"update":false}'
					),
					'permission' => true
				),
				array(
					'title' => 'Email Users &amp; Leaders',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'involvement',
						'both'
					),
					'options' => array(
						'data-core-modal' => '{&quot;update&quot;:false}',
						'escape' => false
					),
					'permission' => true
				),
			);
			$colCount = 2;
			echo $this->element('multiselect', array(
				'colCount' => $colCount,
				'checkAll' => true,
				'links' => $links
			));
			?>
			<tr>
				<th>&nbsp;</th>
				<th><?php echo $this->Paginator->sort('Name', 'Involvement.name');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($involvements as $involvement):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
			?>
			<tr<?php echo $class;?>>
				<td><?php echo $this->MultiSelect->checkbox($involvement['Involvement']['id']); ?></td>
				<td><?php
				echo $this->Html->link($involvement['Ministry']['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $involvement['Ministry']['Campus']['id']), array('escape' => false));
				if (!empty($involvement['Ministry']['ParentMinistry']['id'])) {
					echo ' > ';
					echo $this->Html->link($involvement['Ministry']['ParentMinistry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['ParentMinistry']['id']), array('escape' => false));
				}
				echo ' > ';
				echo $this->Html->link($involvement['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['id']));
				echo ' > ';
				echo $this->Html->link($involvement['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])).$this->Formatting->flags('Involvement', $involvement);
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

<h1>Leader Dashboard</h1>
<div class="content-box">
	<p>Manage the involvement opportunities that you are leading.</p>
<?php
	echo $this->MultiSelect->create();
?>
	<table cellpadding="0" cellspacing="0" id="rosterTable" class="datatable">
		<thead>
			<?php
			$links = array(
				array(
					'title' => 'Email Users',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'compose',
						$this->MultiSelect->token,
						'model' => $model,
						'submodel' => 'Roster'
					),
					'options' => array(
						'rel' => 'modal-none'
					),
					'permission' => true
				),
				array(
					'title' => 'Email Leaders',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'compose',
						$this->MultiSelect->token,
						'model' => $model,
						'submodel' => 'Leader'
					),
					'options' => array(
						'rel' => 'modal-none'
					),
					'permission' => true
				),
				array(
					'title' => 'Email Managers',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'compose',
						$this->MultiSelect->token,
						'model' => $model,
						'submodel' => 'Manager'
					),
					'options' => array(
						'rel' => 'modal-none'
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
				<th><?php echo $this->Paginator->sort('Name', $model.'.name');?></th>
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
				<td><?php echo $this->MultiSelect->checkbox($leader[$model]['id']); ?></td>
				<td><?php echo $this->Html->link($leader[$model]['name'], array('controller' => strtolower(Inflector::pluralize($model)), 'action' => 'view', 'model' => $model, $model => $leader[$model]['id'])).$this->Formatting->flags('Involvement', $leader); ?></td>
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
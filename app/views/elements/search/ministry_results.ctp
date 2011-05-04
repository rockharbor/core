<?php
if (!empty($results)) {
?>
<h3>Results</h3>
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th><?php echo $this->Paginator->sort('name'); ?></th>
				<th><?php echo $this->Paginator->sort('description'); ?></th>
				<th><?php echo $this->Paginator->sort('Campus', 'Campus.name'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	$this->Paginator->options(array(
		 'updateable' => 'results'
	));
	$i = 0;
	foreach ($results as $result):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Html->link($result['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $result['Ministry']['id'])).$this->Formatting->flags('Ministry', $result); ?></td>
			<td><?php echo $this->Text->truncate($result['Ministry']['description'], 250); ?></td>
			<td><?php echo $result['Campus']['name']; ?></td>
		</tr>
<?php	
	endforeach;
?>
		</tbody>
	</table>
	<?php
	echo $this->element('pagination');
} else {
?>
<h3>Results</h3>
<p>No results</p>
<?php 
}
?>
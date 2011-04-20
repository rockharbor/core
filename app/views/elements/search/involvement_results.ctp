<?php
if (!empty($results)) {

$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h3>Results</h3>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
		<th><?php echo $this->Paginator->sort('description'); ?></th>
		<th><?php echo $this->Paginator->sort('Type', 'InvolvementType.name'); ?></th>
		<th><?php echo $this->Paginator->sort('Ministry', 'Ministry.name'); ?></th>
	</tr>
<?php	
	$i = 0;
	foreach ($results as $result):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Formatting->flags('Involvement', $result).$result['Involvement']['name']; ?></td>
			<td><?php echo $result['Involvement']['description']; ?></td>
			<td><?php echo $result['InvolvementType']['name']; ?></td>
			<td><?php echo $result['Ministry']['name']; ?></td>
		</tr>
<?php	
	endforeach;
?>
	</table>
	
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
	</div>
<?php
} else {
?>
<h3>Results</h3>
<p>No results</p>
<?php 
}
?>
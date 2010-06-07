<?php
$this->Paginator->options(array(
	'update' => '#content'
));
?>
<div class="regions index">
	<h2><?php __('Profiles');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th>Actions</th>		
	</tr>
	<?php
	$i = 0;
	foreach ($regions as $region):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $region['Region']['id']; ?>&nbsp;</td>
		<td><?php echo $region['Region']['name']; ?>
		<ul>
			<?php foreach ($region['Zipcode'] as $zipcode) {
				echo '<li>'.$zipcode['zip'].'&nbsp;'.$this->Js->link(
					$this->Html->image('icons'.DS.'delete.png'),
					array('controller' => 'zipcodes', 'action' => 'delete', $zipcode['id']),
					array('complete' => 'CORE.update("content")', 'escape' => false)
				).'</li>';
			}
			?>
		</ul>
		</td>
		<td><?php echo $region['Region']['created']; ?>&nbsp;</td>
		<td><?php echo $region['Region']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('Add Zipcode', array('controller' => 'zipcodes', 'action' => 'add', 'Region' => $region['Region']['id']), array('rel' => 'modal-content')); ?>
			<?php echo $this->Html->link('Edit', array('action' => 'edit', $region['Region']['id'])); ?>
			<?php echo $this->Html->link('Delete', array('action' => 'delete', $region['Region']['id']), array('id' => 'delete_btn_'.$i));?>
		</td>
	</tr>
<?php endforeach; ?>
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
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('New Region', array('action' => 'add'), array('rel' => 'modal-content')); ?></li>
	</ul>
</div>
<?php
while ($i > 0) {
	$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'","Are you sure you want to delete this Region and all its Zip Codes?", {update:"content"});');
	$i--;
}
?>
<?php
$this->Paginator->options(array(
    'update' => '#addresses',
    'evalScripts' => true
));
?>

<div class="related" id="address_view">
	<h3>Addresses</h3>
	<?php if (!empty($data)):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo $this->Paginator->sort('id');?></th>
		<th><?php echo $this->Paginator->sort('name');?></th>
		<th><?php echo $this->Paginator->sort('address_line_1');?></th>
		<th><?php echo $this->Paginator->sort('address_line_2');?></th>
		<th><?php echo $this->Paginator->sort('city');?></th>
		<th><?php echo $this->Paginator->sort('state');?></th>
		<th><?php echo $this->Paginator->sort('zip');?></th>
		<th><?php echo $this->Paginator->sort('lat');?></th>
		<th><?php echo $this->Paginator->sort('lng');?></th>
		<th><?php echo $this->Paginator->sort('primary');?></th>
		<th><?php echo $this->Paginator->sort('active');?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($data as $address):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $address['Address']['id'];?></td>
			<td><?php echo $address['Address']['name'];?></td>
			<td><?php echo $address['Address']['address_line_1'];?></td>
			<td><?php echo $address['Address']['address_line_2'];?></td>
			<td><?php echo $address['Address']['city'];?></td>
			<td><?php echo $address['Address']['state'];?></td>
			<td><?php echo $address['Address']['zip'];?></td>
			<td><?php echo $address['Address']['lat'];?></td>
			<td><?php echo $address['Address']['lng'];?></td>
			<td><?php echo $this->SelectOptions->booleans[$address['Address']['primary']];?></td>
			<td><?php echo $this->SelectOptions->booleans[$address['Address']['active']];?></td>
			<td class="actions">
				<?php 
				echo $this->Html->link('Edit', array('action' => 'edit', $address['Address']['id'], $model => $modelId), array(
					'rel' => 'modal-addresses'
				)); 
				echo $this->Html->link('Delete', array('action' => 'delete', $address['Address']['id'], $model => $modelId), array(
					'id'=>'delete_address_btn_'.$i
				)); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php

endif; 

echo $this->Html->link('Add address', 
	array(
		'action' => 'add', $model => $modelId),
		array(
			'class' => 'button',
			'rel' => 'modal-addresses'
		)
);

?>	
</div>

<?php

$this->Html->scriptStart(array('inline' => true));

while ($i > 0) {
	echo 'CORE.confirmation("delete_address_btn_'.$i.'","Are you sure you want to delete this address?", {update:"addresses"});';
	$i--;
}

echo $this->Html->scriptEnd();

?>
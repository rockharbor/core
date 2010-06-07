<?php

$this->Paginator->options(array(
	'update' => '#content'
));

?>
<div class="payments">
	<h2><?php __('Payments');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('Payment For', 'user_id');?></th>
			<th><?php echo $this->Paginator->sort('Involvement', 'Roster.involvement_id');?></th>
			<th><?php echo $this->Paginator->sort('amount');?></th>
			<th><?php echo $this->Paginator->sort('Payment Type', 'PaymentType.name');?></th>
			<th><?php echo $this->Paginator->sort('number');?></th>
			<th><?php echo $this->Paginator->sort('transaction_id');?></th>
			<th><?php echo $this->Paginator->sort('payment_placed_by');?></th>
			<th><?php echo $this->Paginator->sort('refunded');?></th>
			<th><?php echo $this->Paginator->sort('Payment Option', 'PaymentOption.name');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($payments as $payment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $payment['Payment']['id']; ?>&nbsp;</td>
		<td><?php echo $payment['User']['Profile']['name']; ?>&nbsp;</td>
		<td><?php echo $payment['Roster']['Involvement']['name']; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($payment['Payment']['amount']); ?>&nbsp;</td>
		<td><?php echo $payment['PaymentType']['name']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['number']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['transaction_id']; ?>&nbsp;</td>
		<td><?php echo $payment['Payer']['Profile']['name']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['refunded']; ?>&nbsp;</td>
		<td><?php echo $payment['PaymentOption']['name']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['created']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['modified']; ?>&nbsp;</td>
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
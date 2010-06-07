<?php
$this->Paginator->options(array(
    'update' => '#payment_options', 
    'evalScripts' => true
));
?>
<div class="paymentOptions">
	<h2><?php __('Payment Options');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('total');?></th>
			<th><?php echo $this->Paginator->sort('deposit');?></th>
			<th><?php echo $this->Paginator->sort('childcare');?></th>
			<th><?php echo $this->Paginator->sort('account_code');?></th>
			<th><?php echo $this->Paginator->sort('tax_deductible');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($paymentOptions as $paymentOption):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $paymentOption['PaymentOption']['name']; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($paymentOption['PaymentOption']['total']); ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($paymentOption['PaymentOption']['deposit']); ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($paymentOption['PaymentOption']['childcare']); ?>&nbsp;</td>
		<td><?php echo $paymentOption['PaymentOption']['account_code']; ?>&nbsp;</td>
		<td><?php echo $this->SelectOptions->booleans[$paymentOption['PaymentOption']['tax_deductible']]; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action' => 'edit', $paymentOption['PaymentOption']['id']), array('rel' => 'modal-paymentOptions')); ?>
			<?php echo $this->Html->link('Delete', array('action' => 'delete', $paymentOption['PaymentOption']['id']), array('id' => 'delete_btn_'.$i)); ?>
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
	
	<p>
		<?php echo $this->Html->link('Add Payment Option', array('action' => 'add', 'Involvement' => $involvementId), array('rel'=>'modal-paymentOptions','class'=>'button')); ?>
	</p>
</div>

<?php

while ($i > 0) {
	$this->Js->buffer('CORE.confirmation(\'delete_btn_'.$i.'\',\'Are you sure you want to delete this payment option?\', {update:\'paymentOptions\'});');
	$i--;
}

?>
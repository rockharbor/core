<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
if (empty($this->data)):
?>
<h1>Payments Report</h1>
<div class="clearfix">
<?php
	echo $this->Form->create('Payment', array(
		'default' => false,
		'inputDefaults' => array(
			'empty' => true
		)
	));
	?>
	<fieldset class="grid_5 alpha">
		<legend>Filter by one or the other</legend>
		<?php
		echo $this->Form->input('campus_id');
		echo $this->Form->input('id', array(
			'label' => 'Ministry',
			'options' => $ministries
		));
		echo $this->Form->input('Involvement.name', array(
			'label' => 'Involvement Name'
		));
		?>
	</fieldset>
	<fieldset class="grid_5 omega">
		<legend>Search on each of these</legend>
		<?php
		echo $this->Form->input('PaymentType.id', array(
			'options' => Set::combine($paymentTypes, '/PaymentType/id', '/PaymentType/name')
		));
		echo $this->Form->input('PaymentType.type', array(
			'options' => $paymentTypeTypes
		));
		echo $this->Form->input('PaymentOption.account_code');
		echo $this->Form->input('start_date');
		echo $this->Form->input('end_date');
		?>
	</fieldset>
	<?php
	$defaultSubmitOptions['update'] = '#report';
	echo $this->Js->submit('Get Report', $defaultSubmitOptions);
	echo $this->Form->end();
	?>
</div>
<?php endif;?>
<div id="report">
	<table class="datatable">
		<thead>
			<tr>
				<th>Involvement</th>
				<th>User</th>
				<th>Payment Placed By</th>
				<th><?php echo $this->Paginator->sort('Payment Type', 'PaymentType.name');?></th>
				<th><?php echo $this->Paginator->sort('number');?></th>
				<th><?php echo $this->Paginator->sort('transaction_id');?></th>
				<th>Account Code</th>
				<th><?php echo $this->Paginator->sort('amount');?></th>
				<th><?php echo $this->Paginator->sort('Date', 'created');?></th>
			</tr>
		</thead>
	<?php
		$i = 0;
		foreach ($payments as $payment):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Html->link($payment['Roster']['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $payment['Roster']['Involvement']['id'])); ?></td>
			<td><?php echo $this->Html->link($payment['User']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $payment['User']['id'])); ?></td>
			<td><?php echo empty($payment['Payer']['id']) ? 'System' : $this->Html->link($payment['Payer']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $payment['Payer']['id'])); ?></td>
			<td><?php echo $payment['PaymentType']['name']; ?></td>
			<td><?php
			switch ($payment['PaymentType']['type']) {
				case 2:
					echo '#'.$payment['Payment']['number'];
				break;
				case 0:
					echo 'xxxxxxxxxxxx'.$payment['Payment']['number'];
				break;
			}
			?></td>
			<td><?php echo $payment['Payment']['transaction_id']; ?></td>
			<td><?php echo $payment['Roster']['PaymentOption']['account_code']; ?></td>
			<td><?php echo $this->Formatting->money($payment['Payment']['amount']); ?></td>
			<td><?php echo $this->Formatting->date($payment['Payment']['created']); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('pagination'); ?>
</div>
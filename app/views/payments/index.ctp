<h1>Payments</h1>
<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<div class="payments">
	<div class="grid_10 alpha omega equal-height" style="margin-bottom:10px">
		Sort By:
		<?php
		echo $this->Paginator->sort('Involvement', 'Roster.involvement_id').' / ';
		echo $this->Paginator->sort('payment_placed_by').' / ';
		echo $this->Paginator->sort('Payment Type', 'PaymentType.name').' / ';
		echo $this->Paginator->sort('number').' / ';
		echo $this->Paginator->sort('transaction_id').' / ';
		echo $this->Paginator->sort('Date', 'created').' / ';
		echo $this->Paginator->sort('amount');
		?>
	</div>
	<div class="grid_10 alpha omega equal-height">
		<?php
		$i = 0;
		foreach ($payments as $payment):
			$alphaomega = ($i++ % 2 == 0) ? ' alpha' : ' omega';
		?>
		<div class="grid_5 <?php echo $alphaomega; ?>">
			<div class="receipt">
				<div class="receipt-title">
					<?php
					echo $payment['Roster']['Involvement']['name'];
					echo $this->Html->link('Print', array('controller' => 'payments', 'action' => 'view', 'User' => $user['User']['id'], $payment['Payment']['id'], 'ext' => 'print'), array('class' => 'core-icon icon-print', 'style' => 'float:right;', 'target' => '_blank'));
					?>
				</div>
				<div class="receipt-body">
					<dl>
						<?php
						echo $this->Html->tag('dt', 'Paid By:');
						echo $this->Html->tag('dd', $payment['Payer']['Profile']['name'].'&nbsp;');
						echo $this->Html->tag('dt', 'Paid For:');
						echo $this->Html->tag('dd', $payment['User']['Profile']['name'].'&nbsp;');
						echo $this->Html->tag('dt', 'Date:');
						echo $this->Html->tag('dd', $payment['Payment']['created'].'&nbsp;');
						?>
					</dl>
					<hr>
					<div style="float:left;width:40%;margin-right:5px;" class="border-right">
						<span class="font-large">
							<?php echo $this->Formatting->money($payment['Payment']['amount']); ?>
						</span>
					</div>
					<div style="float:left;width:55%">
						<?php
						echo $this->Html->tag('dd', $payment['PaymentType']['name']);
						switch ($payment['PaymentType']['name']) {
							case 'Check':
								echo $this->Html->tag('dd', '#'.$payment['Payment']['number']);
							break;
							case 'Credit Card':
								echo $this->Html->tag('dd', 'xxxxxxxxxxxx'.$payment['Payment']['number']);
							break;
						}
						$this->Html->tag('dt', 'ID:');
						$this->Html->tag('dd', $payment['Payment']['transaction_id'].'&nbsp;');
						?>
					</div>
					<br clear="all" />
					<?php	if (!empty($payment['Payment']['comment'])): ?>
					<hr>
					<div>
						<?php echo $this->Html->tag('p', $payment['Payment']['comment']); ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
	<br clear="all" />
	<?php echo $this->element('pagination'); ?>
	<ul class="core-admin-tabs">
		<li>
		<?php
		$this->MultiSelect->create();
		echo $this->Html->link('Export',
			array(
				'controller' => 'reports',
				'action' => 'export',
				'Payment',
				$this->MultiSelect->token
			),
			array(
				'rel' => 'modal-none'
			)
		);
		?>
		</li>
	</ul>
</div>
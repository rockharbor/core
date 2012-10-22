<h1>Payments</h1>
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
				<div class="receipt-title core-iconable">
					<?php
					echo $this->Html->link($payment['Roster']['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $payment['Roster']['Involvement']['id']));
					?>
					<div class="core-icon-container">
						<?php
						$icon = $this->element('icon', array('icon' => 'edit'));
						echo $this->Permission->link($icon, array('controller' => 'payments', 'action' => 'edit', $payment['Payment']['id']), array('escape' => false, 'data-core-modal' => 'true', 'class' => 'no-hover'));
						$icon = $this->element('icon', array('icon' => 'print'));
						echo $this->Permission->link($icon, array('controller' => 'payments', 'action' => 'view', 'User' => $user['User']['id'], $payment['Payment']['id'], 'ext' => 'print'), array('target' => '_blank', 'escape' => false, 'class' => 'no-hover'));
						$icon = $this->element('icon', array('icon' => 'delete'));
						echo $this->Permission->link($icon, array('controller' => 'payments', 'action' => 'delete', $payment['Payment']['id']), array('escape' => false, 'id' => 'delete_btn_'.$i, 'class' => 'no-hover'));
						$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'","Are you sure you want to remove this payment for '.$this->Formatting->money($payment['Payment']['amount']).'?", {update:true});');
						?>
					</div>
				</div>
				<div class="receipt-body">
					<dl>
						<?php
						echo $this->Html->tag('dt', 'Paid By:');
						if ($this->Permission->check(array('controller' => 'profiles', 'action' => 'view', 'User' => $payment['Payer']['Profile']['user_id']))) {
							$link = $this->Html->link($payment['Payer']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $payment['Payer']['Profile']['user_id']));
						} else {
							$link = $payment['Payer']['Profile']['name'];
						}
						echo $this->Html->tag('dd', $link.'&nbsp;');
						echo $this->Html->tag('dt', 'Paid For:');
						if ($this->Permission->check(array('controller' => 'profiles', 'action' => 'view', 'User' => $payment['User']['Profile']['user_id']))) {
							$link = $this->Html->link($payment['User']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $payment['User']['Profile']['user_id']));
						} else {
							$link = $payment['User']['Profile']['name'];
						}
						echo $this->Html->tag('dd', $link.'&nbsp;');
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
						switch ($payment['PaymentType']['type']) {
							case 2:
								echo $this->Html->tag('dd', '#'.$payment['Payment']['number']);
							break;
							case 0:
								echo $this->Html->tag('dd', 'xxxxxxxxxxxx'.$payment['Payment']['number']);
							break;
						}
						echo $this->Html->tag('dt', 'ID: ');
						echo $this->Html->tag('dd', $payment['Payment']['transaction_id'].'&nbsp;');
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
				'mstoken' => $this->MultiSelect->token
			),
			array(
				'data-core-modal' => '{"update":false}'
			)
		);
		if (isset($this->passedArgs['Roster'])) {
			$link = $this->Permission->link('Add', array('controller' => 'payments', 'action' => 'add', $this->passedArgs['Roster']), array('data-core-modal' => '{"update":false}'));
			if ($link) {
				echo $this->Html->tag('li', $link);
			}
		}
		?>
		</li>
	</ul>
</div>
<div style="width:500px">
	<div class="receipt">
		<div class="receipt-title">
			<?php
			echo $payment['Roster']['Involvement']['name'];
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
				switch ($payment['PaymentType']['type']) {
					case 2:
						echo $this->Html->tag('dd', '#'.$payment['Payment']['number']);
					break;
					case 0:
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
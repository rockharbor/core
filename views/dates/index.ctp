<?php
$exemptions = Set::extract('/Date[exemption=1]', $dates);
$dates = Set::extract('/Date[exemption=0]', $dates);
?>
<h1><?php __('Dates');?></h1>
<div class="dates content-box">
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th>Happens</th>
				<th width="40px">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach ($dates as $date):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = 'altrow';
			}
		?>
		<tr class="core-iconable <?php echo $class;?>">
			<td><?php echo $this->Formatting->readableDate($date); ?></td>
			<td width="40px"><div class="core-icon-container"><?php
			$icon = $this->element('icon', array('icon' => 'edit'));
			echo $this->Permission->link($icon, array('action' => 'edit', $date['Date']['id'], 'Involvement' => $involvementId), array('data-core-modal' => 'true', 'class' => 'no-hover', 'escape' => false));
			$icon = $this->element('icon', array('icon' => 'delete'));
			echo $this->Permission->link($icon, array('action' => 'delete', $date['Date']['id'], 'Involvement' => $involvementId), array('id' => 'delete-date-btn-'.$date['Date']['id'], 'class' => 'no-hover', 'escape' => false));
			$this->Js->buffer('CORE.confirmation("delete-date-btn-'.$date['Date']['id'].'","Are you sure you want to delete this date?", {update:true});');
			?></div>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th>Except</th>
				<th width="40px">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach ($exemptions as $date):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = 'altrow';
			}
		?>
		<tr class="core-iconable <?php echo $class;?>">
			<td><?php echo $this->Formatting->readableDate($date); ?></td>
			<td width="40px"><div class="core-icon-container"><?php
			$icon = $this->element('icon', array('icon' => 'edit'));
			echo $this->Permission->link($icon, array('action' => 'edit', $date['Date']['id'], 'Involvement' => $involvementId), array('data-core-modal' => 'true', 'class' => 'no-hover', 'escape' => false));
			$icon = $this->element('icon', array('icon' => 'delete'));
			echo $this->Permission->link($icon, array('action' => 'delete', $date['Date']['id'], 'Involvement' => $involvementId), array('id' => 'delete-date-btn-'.$date['Date']['id'], 'class' => 'no-hover', 'escape' => false));
			$this->Js->buffer('CORE.confirmation("delete-date-btn-'.$date['Date']['id'].'","Are you sure you want to delete this date?", {update:true});');
			?></div>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p>
	<?php echo $this->Permission->link('New Date', array('action'=>'add', 'Involvement' => $involvementId), array('data-core-modal' => 'true', 'class'=>'button')); ?>
	</p>
</div>

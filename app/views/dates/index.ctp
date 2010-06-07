<?php

echo $this->Html->script('super_date');

?>

<div class="dates">
	<h2><?php __('Dates');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Date</th>
			<th>Exemption</th>
			<th class="actions">Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($dates as $date):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td id="hr_<?php echo $i; ?>">
		<?php
			$this->Js->buffer('$("#hr_'.$i.'").text(CORE.makeHumanReadable({
				recurring: '.$date['Date']['recurring'].',
				type: \''.$date['Date']['recurrance_type'].'\',
				frequency: '.$date['Date']['frequency'].',
				day: '.$date['Date']['day'].',				
				weekday: '.$date['Date']['weekday'].',
				offset: '.$date['Date']['offset'].',
				allday: '.$date['Date']['all_day'].',
				permanent: '.$date['Date']['permanent'].',
				startDate: \''.$date['Date']['start_date'].'\',
				endDate: \''.$date['Date']['end_date'].'\',
				startTime: \''.$date['Date']['start_time'].'\',
				endTime: \''.$date['Date']['end_time'].'\'
			}))', array('onDomReady' => false));
		?>
		</td>
		<td>
			<?php echo $date['Date']['exemption']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action' => 'edit', $date['Date']['id']), array('rel' => 'modal-dates')); ?>
			<?php echo $this->Html->link('Delete', array('action' => 'delete', $date['Date']['id']), array('id' => 'delete_btn_'.$i)); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>

<p>
<?php echo $this->Html->link('New Date', array('action'=>'add', 'Involvement' => $involvementId), array('rel'=>'modal-dates','class'=>'button')); ?>
</p>

<?php
while ($i > 0) {
	$this->Js->buffer('CORE.confirmation(\'delete_btn_'.$i.'\',\'Are you sure you want to delete this date?\', {update:\'dates\'});');
	$i--;
}
?>
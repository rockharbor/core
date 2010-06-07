<?php
$this->Paginator->options(array(
    'update' => '#content', 
    'evalScripts' => true
));
?>

<div class="rosters">
	<h2>My Involvement</h2>
	<p><?php
	if ($passed == 'passed') {
		echo $this->Html->link('Hide past involvement', array('action' => 'involvement', 'User'=>$userId), array('class' => 'button'));
	} else {
		echo $this->Html->link('Show past involvement', array('action' => 'involvement', 'User'=>$userId, 'passed'), array('class' => 'button'));
	}
	?></p>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('Involvement', 'Involvement.name');?></th>
			<th><?php echo $this->Paginator->sort('Joined', 'created');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($rosters as $roster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Formatting->flags('Involvement', $roster).$this->Html->link($roster['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $roster['Involvement']['id'])) ; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->date($roster['Roster']['created']); ?>&nbsp;</td>
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

<div id="involvementCalendar">
<?php
echo $this->element('calendar', array(
	'filters' => array(
		$passed,
		'model' => 'User',
		'User' => $userId
	)
));
?>
</div>
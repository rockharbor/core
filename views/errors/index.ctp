<?php
$this->Paginator->options(array(
    'update' => '#content'
));
?>

<div class="logs">
	<h2>Errors</h2>
	
	<p>Quick filter:</p>
	<p>
	<?php
		echo $this->Js->link('All', array(), array('update' => '#content'));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Warning', array('warning'), array('update' => '#content'));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Notice', array('notice'), array('update' => '#content'));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Deprecated', array('deprecated'), array('update' => '#content'));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Parse', array('parse'), array('update' => '#content'));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('User Warning', array('user_warning'), array('update' => '#content'));
	?>
	</p>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('level');?></th>
			<th><?php echo $this->Paginator->sort('message');?> / <?php echo $this->Paginator->sort('file');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($logs as $log):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $log['Error']['level']; ?>&nbsp;</td>
		<td><?php echo $log['Error']['message'].' ('.$log['Error']['file'].': '.$log['Error']['line'].')'; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->date($log['Error']['created']); ?>&nbsp;</td>
		
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
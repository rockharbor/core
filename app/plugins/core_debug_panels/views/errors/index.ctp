<?php
$divId = '[id=core_debug_panels.errors-tab]>div>div>div';
$this->Paginator->options(array(
    'update' => $divId
));
?>

<div class="logs">
	<h2>PHP Errors</h2>

	<p>Quick filter:</p>
	<p>
	<?php
		echo $this->Js->link('All', array('controller' => 'errors'), array('update' => $divId));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Warning', array('controller' => 'errors', 'warning'), array('update' => $divId));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Notice', array('controller' => 'errors', 'notice'), array('update' => $divId));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Deprecated', array('controller' => 'errors', 'deprecated'), array('update' => $divId));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('Parse', array('controller' => 'errors', 'parse'), array('update' => $divId));
		echo '&nbsp;&nbsp;';
		echo $this->Js->link('User Warning', array('controller' => 'errors', 'user_warning'), array('update' => $divId));
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
	foreach ($content as $error):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $error['Error']['level']; ?>&nbsp;</td>
		<td><?php echo $error['Error']['message'].' ('.$error['Error']['file'].': '.$error['Error']['line'].')'; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->date($error['Error']['created']); ?>&nbsp;</td>

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

<?php
echo $this->Js->writeBuffer(array(
	'onDomReady' => false
));
?>
<style type="text/css">
div [id="core_debug_panels.errors-tab"] table a {
	background:none !important;
	border:none !important;
	display:inline !important;
	float:none !important;
}
div [id="core_debug_panels.errors-tab"] a {
	display:inline !important;
	float:none !important;
}
</style>
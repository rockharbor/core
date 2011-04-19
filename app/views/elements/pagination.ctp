<?php
$updateable = false;
if (isset($this->Paginator->options['updateable'])) {
	$updateable = $this->Paginator->options['updateable'];
	unset($this->Paginator->options['updateable']);
}
$id = uniqid('pagination-');
?>
<div id="<?php echo $id; ?>" class="pagination clearfix">
	<span style="float:left">
	<?php
	echo $this->Paginator->prev('Prev', array('class' => 'button'), null, array('class' => 'button disabled'));
	echo $this->Paginator->next('Next', array('class' => 'button'), null, array('class' => 'button disabled'));
	?></span>
	<span style="float:right;line-height:30px">
		<?php echo $this->Paginator->counter(array('format' => 'Records: %start%-%end% of %count%')); ?>
	</span>
</div>
<?php
if ($updateable) {
	$this->Js->buffer("CORE.updateablePagination('$id', '$updateable')");
}
?>
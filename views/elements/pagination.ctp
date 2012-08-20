<?php
$id = uniqid('pagination-');
?>
<div class="pagination clearfix" id="<?php echo $id; ?>">
	<?php
	if ($this->Paginator->counter(array('format' => '%pages%')) == 1) {
		if ($this->Paginator->counter(array('format' => '%count%')) == 0) {
			echo $this->Html->tag('p', 'No records found.');
		}
	} else {
	?>
		<span style="float:left">
		<?php
		echo $this->Paginator->prev('Prev', array('class' => 'button'), null, array('class' => 'button disabled'));
		echo $this->Paginator->next('Next', array('class' => 'button'), null, array('class' => 'button disabled'));
		?></span>
		<span style="float:right;line-height:30px">
			<?php echo $this->Paginator->counter(array('format' => 'Records: %start%-%end% of %count%')); ?>
		</span>
	<?php
	}
	?>
</div>
<?php
$this->Js->buffer("CORE.updateablePagination('$id')");
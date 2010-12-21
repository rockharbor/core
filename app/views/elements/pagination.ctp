<?php
if (!isset($colCount)) {
	$colCount = 3;
}
?>
<tr class="pagination">
	<td colspan="2"><?php
		echo $this->Paginator->prev('Prev', array('class' => 'button'), null, array('class' => 'button disabled'));
		echo $this->Paginator->next('Next', array('class' => 'button'), null, array('class' => 'button disabled'));
	?></td>
	<td colspan="<?php echo $colCount-2; ?>" style="text-align: right;"><?php
		echo $this->Paginator->counter(array('format' => 'Records: %start%-%end% of %count%'));
	?></td>
</tr>
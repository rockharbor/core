<?php
if (!isset($colCount)) {
	$colCount = 1;
}
if (!isset($checkAll)) {
	$checkAll = false;
}
if (!isset($links)) {
	return;
}
?>
<tr class="multi-select">
	<th><?php
		if ($checkAll) {
			echo $this->MultiSelect->checkbox('all');
		}
		?>
	</th>
	<th colspan="<?php echo $colCount-1; ?>">
	<?php
	foreach ($links as $link) {
		$_default = array(
			'title' => '',
			'url' => '/',
			'options' => array(),
			'needCheckAll' => false
		);
		$link = array_merge($_default, $link);
		if ($this->Permission->check($link['url'])) {
			echo $this->Html->link($link['title'], $link['url'], $link['options']);
		}
	}
	?>
	</th>
</tr>

<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
$this->MultiSelect->create();
?>
<table cellpadding="0" cellspacing="0" class="datatable">
	<thead>
		<?php 
		$colCount = 2;
		echo $this->element('search'.DS.'actions'.DS.$element, compact('result', 'id', 'colCount'));
		?>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 0;
		foreach ($results as $result):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
			<tr<?php echo $class;?>>
				<td><?php echo $this->MultiSelect->checkbox($result['Involvement']['id']); ?></td>
				<td><?php echo $result['Involvement']['name'].$this->Formatting->flags('Involvement', $result); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php 
echo $this->element('pagination');
echo $this->MultiSelect->end();

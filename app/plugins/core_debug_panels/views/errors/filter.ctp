<table cellpadding="0" cellspacing="0">
		<tr>
			<th>Level</th>
			<th>Message / File</th>
			<th>Occured</th>
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
			<td><?php echo $error['Error']['created']; ?>&nbsp;</td>

		</tr>
	<?php endforeach; ?>
</table>
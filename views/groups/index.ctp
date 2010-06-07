<div class="groups">
	<h2>Groups</h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Name</th>
			<th class="actions">Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($groups as $groupid => $groupname):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $groupname; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Js->link('Edit', array('action' => 'edit', $groupid),
				array(
					'rel'=>'modal-content'
				)
			); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>
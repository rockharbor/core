<h2>Visit History</h2>
<p>Last <?php echo count($content); ?> requests</p>
<table>
<?php
foreach ($content as $history):
?>
	<tr>
		<td><?php echo $history; ?></td>
	</tr>
<?php
endforeach;
?>
</table>
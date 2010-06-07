<h2>Bug Details</h2>
<p>Problem:<br /><em>[please briefly describe the issue]</em></p>
<p>Replication:<br /><em>[please describe how to replicate the problem]</em></p>
<h2>User Details</h2>
<p>
<table>
	<tr>
		<th>Field</th>
		<th>Value</th>
	</tr>
	<tr>
		<td>Username</td>
		<td><?php echo $activeUser['User']['username']; ?></td>
	</tr>
	<tr>
		<td>Group(s)</td>
		<td><?php echo implode(', ', Set::extract('/Group/id', $activeUser)); ?></td>
	</tr>
	<tr>
		<td>Active</td>
		<td><?php echo $activeUser['User']['active']; ?></td>
	</tr>
	<tr>
		<td>Child</td>
		<td><?php echo $activeUser['Profile']['child']; ?></td>
	</tr>
</table>

</p>
<h2>Activity Details</h2>
<p>
<table>
	<tr>
		<th>Setting</th>
		<th>Detail</th>
	</tr>
	<tr>
		<td>Page</td>
		<td><?php echo $visitHistory[count($visitHistory)-1]; ?></td>
	</tr>
	<tr>
		<td>User Agent</td>
		<td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
	</tr>
</table>
</p>
<p>Last 10 visits (newest first):<br />
<ol>
<?php 
$i = 0;
while ($visitHistory[count($visitHistory)-(2+$i)] && $i < 10) {
?>
	<li><?php echo $visitHistory[count($visitHistory)-(2+$i)]; ?></li>
<?php $i++; } ?>
</ol>
</p>
</p>
<p>Last 10 errors (newest first):<br />
<ol>
<?php 
foreach ($errors as $error) {
?>
	<li><?php echo $error['Error']['level']; ?>: <?php echo $error['Error']['message'].' ('.$error['Error']['file'].': '.$error['Error']['line'].')'; ?> on <?php echo $error['Error']['created']; ?></li>
<?php 
} 
?>
</ol>
</p>
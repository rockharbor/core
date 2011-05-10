<h2>Auth Group</h2>
<p id="authGroup">
	Your group is currently <strong><?php echo $activeUser['Group']['name']; ?></strong>.
</p>
<p>Choose a group to temporarily switch to:
<?php echo $this->Form->input('group_id', array(
	'type' => 'select',
	'label' => false,
	'options' => $content['groups'],
	'empty' => true
)); ?>
</p>
<h2>Auth History</h2>
<p>Last <?php echo count($content); ?> requests</p>
<table>
<?php
foreach ($content['history'] as $history):
?>
	<tr>
		<td><?php echo $history; ?></td>
	</tr>
<?php
endforeach;
?>
</table>
<script type="text/javascript">
$("#group_id").bind("change", function() {
	$.ajax({
		url: "<?php echo Router::url(array(
			'controller' => 'cdp_groups',
			'action' => 'swap',
			'plugin' => 'core_debug_panels'			
		)); ?>/"+$("#group_id").val(),
		success: function(data) {
			$("#authGroup").html(data);
		}
	})
});
</script>
<h2>PHP Errors</h2>
<p>Quick Filter: 
	<?php
	echo $this->Form->input('filter', array(
		'label' => false,
		'type' => 'select',
		'options' => array(
			'all' => 'All',
			'warning' => 'Warning',
			'notice' => 'Notice',
			'deprecated' => 'Deprecated',
			'update' => 'Update',
			'user_warning' => 'User_warning'
		),
		'selected' => 'all'
	));
	?>
</p>
<p>Display:
	<?php
	echo $this->Form->input('display', array(
		'label' => false,
		'type' => 'select',
		'options' => array(
			'10' => '10',
			'20' => '20',
			'50' => '50',
			'100' => '100'
		),
		'selected' => 10
	));
	?>
</p>
<div id="errors">
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
</div>

<script type="text/javascript">
$("#filter, #display").bind("change", function() {
	$.ajax({
		url: "<?php echo Router::url(array(
			'controller' => 'cdp_errors',
			'action' => 'filter',
			'plugin' => 'core_debug_panels'
		)); ?>/"+$("#filter").val()+"/"+$("#display").val(),
		success: function(data) {
			$("#errors").html(data);
		}
	})
});
</script>
<style type="text/css">
div [id="core_debug_panels.errors-tab"] table a {
	background:none !important;
	border:none !important;
	display:inline !important;
	float:none !important;
}
div [id="core_debug_panels.errors-tab"] a {
	display:inline !important;
	float:none !important;
}
</style>
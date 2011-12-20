<?php 
$data = unserialize($data);
if (!empty($data)): 
?>
<div class="box" style="margin-bottom: 20px;">
<p>Some information could not be transferred to the new CORE. Don't worry, we saved
it so you don't have to remember what it was. Fill in the new fields with the
information below.</p>
<dl>
	<?php 
	foreach ($data as $field => $info) {
		echo $this->Html->tag('dt', Inflector::humanize($field).':');
		echo $this->Html->tag('dd', $info.'&nbsp;');
	}
	?>
</dl>
</div>
<?php endif; ?>
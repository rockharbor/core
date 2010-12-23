<h1>Edit App Setting</h1>
<div class="appSettings">
<?php echo $this->Form->create('AppSetting', array('default' => false));?>
	<fieldset>
 		<legend><?php 
		$appSettingName = explode('.', $this->data['AppSetting']['name']);
		$appSettingName = $appSettingName[1];
		echo Inflector::humanize($appSettingName); 
		?></legend>
		<p><?php echo $this->data['AppSetting']['description']; ?></p>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('type');
		echo $this->Form->hidden('name');
		echo $this->Form->hidden('description');
		echo $this->Form->input('value');
	?>
	</fieldset>
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<?php
if ($this->data['AppSetting']['type'] == 'html') {
	echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
	echo $this->Html->css('jquery.wysiwyg');
	$this->Js->buffer('CORE.wysiwyg("AppSettingValue");');
}
if (isset($model)) {
	$this->Js->buffer('CORE.autoComplete("AppSettingValue", "/app_settings/search/'.$model.'.json", function(item) {
		$("#AppSettingValue").val(item.id);
	})');
}
?>
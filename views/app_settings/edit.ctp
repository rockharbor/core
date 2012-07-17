<h1>Edit App Setting</h1>
<div class="appSettings">
<?php if ($this->data['AppSetting']['type'] == 'image'):
	$appSettingName = explode('.', $this->data['AppSetting']['name']);
	$appSettingName = $appSettingName[1];
	echo Inflector::humanize($appSettingName).'<br />';
	if (!empty($this->data['Image']['id'])) {
		echo $this->Media->embed($this->data['Image']['dirname'].DS.$this->data['Image']['basename'], array('restrict' => 'image'));
		echo '<br />';
		echo $this->Html->link('Delete', array('controller' => 'app_setting_images', 'action' => 'delete', $this->data['Image']['id']), array('id' => 'delete_appsetting_image_'.$this->data['AppSetting']['id'], 'class' => 'button'));
		$this->Js->buffer('CORE.confirmation("delete_appsetting_image_'.$this->data['AppSetting']['id'].'", "Are you sure you want to delete this image?", {update:"content"})');
	} else {
		echo $this->element('upload', array(
			'model' => 'AppSetting',
			'AppSetting' => $this->data['AppSetting']['id'],
			'type' => 'Image'
		));
	}
else: ?>
	<?php echo $this->Form->create('AppSetting', array('default' => false));?>
		<fieldset>
			<legend><?php
			$appSettingName = explode('.', $this->data['AppSetting']['name']);
			$appSettingName = $appSettingName[1];
			echo Inflector::humanize($appSettingName).'<br />';
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
<?php endif; ?>
</div>

<?php
if ($this->data['AppSetting']['type'] == 'html') {
	$this->Js->buffer('CORE.wysiwyg("AppSettingValue");');
}
if (isset($model)) {
	$this->Js->buffer('CORE.autoComplete("AppSettingValue", "/app_settings/search/'.$model.'.json", function(item) {
		$("#AppSettingValue").val(item.id);
	})');
}

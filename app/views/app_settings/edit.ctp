<div class="appSettings">
<h2>Edit App Setting</h2>
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
		if (empty($this->data['AppSetting']['model'])) {
			echo $this->Form->input('value');
		} else {
			echo $this->Form->input('value', array(
				'type' => 'select',
				'options' => $valueOptions
			));
		}
	?>
	</fieldset>
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<?php
if ($this->data['AppSetting']['html']) {
	echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
	echo $this->Html->css('jquery.wysiwyg');
	$this->Js->buffer('CORE.wysiwyg(\'AppSettingValue\');');
}
?>
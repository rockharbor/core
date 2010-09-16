<?php echo $this->Html->script('jquery.plugins/jquery.form'); ?>

<?php
/**
 * To debug, comment the 'ext' key from the form url, and the Js buffer at the end
 */
$uid = uniqid();
$settingName = Inflector::pluralize(strtolower($model)).'.'.strtolower($attachmentModel).'_limit';
if (count($attachments) < (Core::read($settingName) !== null ? Core::read($settingName) : 1)) {
	echo $this->Form->create($model, array(
		'type' => 'file',
		'url' => array(
			'action' => 'upload',
			'controller' => Inflector::tableize($model.$attachmentModel),
			'model' => $model,
			$model => $modelId,
			'ext' => 'json'
		),
		'id' => 'Upload'.$model.'Form'.$uid
	));

?>
<fieldset>
	<legend>Upload <?php echo Inflector::humanize($attachmentModel); ?></legend>
<?php
	echo $this->Form->hidden($attachmentModel.'.foreign_key', array('value' => $modelId));
	echo $this->Form->hidden($attachmentModel.'.model', array('value' => $model));
	echo $this->Form->hidden($attachmentModel.'.group', array('value' => $attachmentModel));
	echo $this->Form->file($attachmentModel.'.file', array(
		'id' => $attachmentModel.'File'.$uid
	));

	echo $this->Form->end('Upload');

	$this->Js->buffer('CORE.ajaxUpload("Upload'.$model.'Form'.$uid.'", "'.$attachmentModel.'Attachments");');
?>
</fieldset>
<?php
}
?>
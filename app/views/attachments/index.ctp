<?php echo $this->Html->script('jquery.plugins/jquery.form'); ?>

<?php

$uid = uniqid();

$i = 0;
foreach ($attachments as $attachment) {	
	$item = $attachment[$attachmentModel];

	$file = $this->Media->file($item['file']);
	
	if ($file) {
		$url = $this->Media->url($file);
		
 		$Media = Media::factory($file);
		$size = $this->Media->size($file);
		if (isset($this->Number)) {
			$size = $this->Number->toReadableSize($size);
		} else {
			$size .= ' Bytes';
		}

		printf('<div class="description">%s&nbsp;(%s/%s) <em>%s</em> %s</div>',
			$url ? $this->Html->link($item['basename'], $url) : $item['basename'],
			strtolower($Media->name), $size, $item['alternative'],
			$this->Html->link('[Delete]', 
				array(
					'action' => 'delete',
					'model' => $model,
					$model => $modelId,
					$attachment[$attachmentModel]['id']
				), 
				array(
					'id' => 'delete_btn_'.$i.'_'.$uid
				)
			)
		);
	}

		$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'_'.$uid.'","Are you sure you want to delete this '.Inflector::humanize($attachmentModel).'?", {update:"'.$attachmentModel.'Attachments"});');

	$i++;

	}
?>

	
<?php
/**
 * To debug, comment the 'ext' key from the form url, and the Js buffer at the end
 */
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
}
?>
	</fieldset>
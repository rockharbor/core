<?php
/**
 * To debug, comment the 'ext' key from the form url, and the Js buffer at the end
 */
$settingName = Inflector::pluralize(strtolower($model)).'.'.strtolower($attachmentModel).'_limit';
if (count($attachments) < (Core::read($settingName) !== null ? Core::read($settingName) : 1)) {
	echo $this->element('upload', array(
		'model' => $model,
		$model => $modelId,
		'type' => $attachmentModel
	));
}

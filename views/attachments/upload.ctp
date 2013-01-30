<?php
/**
 * To debug, comment the 'ext' key from the form url, and the Js buffer at the end
 */
if (count($attachments) < $limit) {
	echo $this->element('upload', array(
		'model' => $model,
		$model => $modelId,
		'type' => $attachmentModel
	));
}

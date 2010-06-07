<?php echo $this->Html->script('jquery.plugins/jquery.jup'); ?>

<?php

$uid = uniqid();

$i = 0;
foreach ($attachments as $attachment) {	
	$item = $attachment[$attachmentModel];
	$previewVersion = 'xss';

	if ($file = $this->Media->file($item)) {
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

/*
To debug "ajax" uploading, comment out the JavasScript below where the
jup function is called.
--OR--
Firebug the iframe that jUp creates at the end of the DOM
*/
if (count($attachments) < (isset($CORE['settings'][strtolower($model).'_'.strtolower($attachmentModel).'_limit']) ? $CORE['settings'][strtolower($model).'_'.strtolower($attachmentModel).'_limit'] : 1)) {
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
	echo $this->Form->file($attachmentModel.'.0.file', array(
		'id' => $attachmentModel.'File'.$uid
	));
	echo '<div id="error'.$uid.'"></div>';

	echo $this->Form->end('Upload');

	$this->Html->scriptStart(array('inline'=>true));
	echo '$("#Upload'.$model.'Form'.$uid.'").jup({
		onComplete: function(response, formId) {
			var e = $("#error'.$uid.'");
			if (response.length == 0) {
				CORE.update("'.$attachmentModel.'Attachments");
			} else if (response == false) {				
				var msg = "Unknown error."
				e.text(msg);
				e.addClass("error-message");
			} else {
				var msg = response.'.$attachmentModel.'.file;
				$("#'.$attachmentModel.'File'.$uid.'").addClass("error");
				e.text(msg);
				e.addClass("error-message");				
			}
		}
	})';
	echo $this->Html->scriptEnd();
}
?>
	</fieldset>



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
					'id' => 'delete_btn_'.$uid
				)
			)
		);
	}

		$this->Js->buffer('CORE.confirmation("delete_btn_'.$uid.'","Are you sure you want to delete this '.Inflector::humanize($attachmentModel).'?", {update:"'.$attachmentModel.'Attachments"});');

	$i++;

	}
?>
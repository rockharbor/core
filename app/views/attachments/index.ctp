<h1><?php echo Inflector::humanize($attachmentModel); ?></h1>
<div>
	<div class="attachments clearfix">
	<?php
	$i = 0;
	foreach ($attachments as $attachment) {
		$item = $attachment[$attachmentModel];

		$file = $this->Media->file($item['file']);

		if ($file) {
			$Media = Media::factory($file);
			$size = $this->Media->size($file);
			if (isset($this->Number)) {
				$size = $this->Number->toReadableSize($size);
			} else {
				$size .= ' Bytes';
			}
			$class = 'attachment';
			$class .= $i % 3 == 0 ? ' border-right' : '';

		?>
		<div style="width:33%;float:left" class="<?php echo $class; ?>">
			<?php echo $this->Html->link($item['alternative'], array('action' => 'download', $item['id']), array('class' => 'download')); ?>
			<dl>
				<?php
				echo $this->Html->tag('dt', 'Uploaded:');
				echo $this->Html->tag('dd', $this->Formatting->date($item['created']).'&nbsp;');
				$delete = $this->Permission->link('Delete',
					array(
						'controller' => $this->params['controller'],
						'action' => 'delete',
						'model' => $model,
						$model => $modelId,
						$attachment[$attachmentModel]['id']
					),
					array(
						'id' => 'delete_btn_'.$item['id']
					)
				);
				if ($delete) {
					$delete = ' | '.$delete;
				}
				echo $this->Html->tag('dd', $size.$delete);
				$this->Js->buffer('CORE.confirmation("delete_btn_'.$item['id'].'","Are you sure you want to delete this '.Inflector::humanize($attachmentModel).'?", {update:"'.$attachmentModel.'Attachments"});');
				$i++;
				?>
			</dl>
		</div>
		<?php
		}
	}
	?>
	</div>
	<?php
	$settingName = Inflector::pluralize(strtolower($model)).'.'.strtolower($model).'_'.strtolower($attachmentModel).'_limit';
	if (count($attachments) < (Core::read($settingName) !== null ? Core::read($settingName) : 1)) {
		echo $this->element('upload', array(
			'model' => $model,
			$model => $modelId,
			'type' => $attachmentModel
		));
	}
	?>
</div>
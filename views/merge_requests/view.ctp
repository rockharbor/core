<div class="merge_requests">
<h2>Merge <?php echo Inflector::humanize($model);?></h2>
	<?php echo $this->element('merge/'.strtolower($model), array('result' => $result)); ?>
	<?php echo $this->Js->link('Merge '.Inflector::humanize($model), 
		array(
			'controller' => 'merge_requests',
			'action' => 'merge',
			$result['MergeRequest']['id']
		),
		array(
			'class' => 'button',
			'complete' => 'CORE.closeModals()'
		)
	); ?>
</div>
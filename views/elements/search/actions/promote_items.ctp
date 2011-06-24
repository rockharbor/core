<?php

$links = array(
	array(
		'title' => 'Promote '.$model,
		'url' => array(
			'controller' => strtolower(Inflector::underscore($model.'Images')),
			'action' => 'promote',
			$this->MultiSelect->token,
			1
		),
		'options' => array(
			'success' => 'CORE.showFlash(data);'
		)
	)
);

echo $this->element('multiselect', compact('links', 'colCount'));

?>
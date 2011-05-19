<?php
$links = array(
	array(
		'title' => 'Add to household',
		'url' => array(
			'controller' => 'households',
			'action' => 'shift_households',
			$this->MultiSelect->token,
			$named['Household']
		),
		'options' => array(
			'success' => 'CORE.showFlash(data);'
		)
	)
);

echo $this->element('multiselect', compact('links', 'colCount'));

?>
<?php

class ErrorsPanel extends DebugPanel {

/*
 * Plugin name
 *
 * @var string
 */
	var $plugin = 'core_debug_panels';

/*
 * The name of the element to load
 *
 * @var string
 */
	var $elementName = 'errors_panel';

/**
 * The title of the panel
 *
 * @var string
 */
	var $title = 'Errors';

	function beforeRender($controller) {
		App::import('Model', 'CoreDebugPanels.Error');
		$Error = new Error();

		$Error->recursive = 0;
		$Error->order = 'Error.created DESC';

		return $Error->find('all', array(
			'limit' => 10
		));
	}

}

?>
<?php

class VisitHistoryPanel extends DebugPanel {

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
	var $elementName = 'visit_history_panel';

/**
 * The title of the panel
 *
 * @var string
 */
	var $title = 'Visit History';

/**
 * Number of history elements to keep. 0 for unlimited
 *
 * @var string
 **/
	var $history = 10;

/*
 * Loads and saves newest request
 *
 * @param object $controller The calling controller
 */
	function startup(&$controller) {
		if (!$controller->Session->check('CoreDebugPanels.visitHistory')) {
			$controller->Session->write('CoreDebugPanels.visitHistory', array());
		}

		$visitHistory = $controller->Session->read('CoreDebugPanels.visitHistory');
		$visitHistory[] = $controller->here;

		if ($this->history > 0) {
			while (count($visitHistory) > $this->history) {
					array_shift($visitHistory);
			}
		}

		$controller->Session->write('CoreDebugPanels.visitHistory', $visitHistory);
	}

/*
 * Displays history
 *
 * @param object $controller The calling controller
 */
	function beforeRender(&$controller) {
		if ($controller->Session->check('CoreDebugPanels.visitHistory')) {
			return array_reverse($controller->Session->read('CoreDebugPanels.visitHistory'));
		} else {
			return array();
		}
	}

}

?>
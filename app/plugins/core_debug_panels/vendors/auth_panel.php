<?php

class AuthPanel extends DebugPanel {

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
	var $elementName = 'auth_panel';

/**
 * The title of the panel
 *
 * @var string
 */
	var $title = 'Auth';

/*
 * Should we log all the requests in addition to keeping them in the session?
 *
 * @var boolean
 */
	var $log = true;

/**
 * Number of auth history elements to keep. 0 for unlimited
 *
 * @var string
 **/
	var $history = 10;

/*
 * Loads and saves newest auth request
 *
 * Relies on CORE's use of AppController::activeUser as well as AppController::_setConditionalGroups()
 *
 * @param object $controller The calling controller
 */
	function startup(&$controller) {
		if (!$controller->activeUser || !$controller->Session->check('CoreDebugPanels.authHistory')) {
			$controller->Session->write('CoreDebugPanels.authHistory', array());
			return;
		}

		$this->_logAccess($controller);
	}

/*
 * Displays history
 *
 * @param object $controller The calling controller
 */
	function beforeRender(&$controller) {
		if ($controller->Session->check('CoreDebugPanels.authHistory')) {
			$history = array_reverse($controller->Session->read('CoreDebugPanels.authHistory'));
		} else {
			$history = array();
		}

		$content = array(
			'history' => $history,
			'groups' => ClassRegistry::init('Group')->generatetreelist(array(
				'conditional' => false
			))
		);

		return $content;
	}

/**
 * Checks user access and logs it accordingly
 *
 * @param object $controller The calling controller
 * @return void
 * @access protected
 */
	function _logAccess($controller) {
		$model = 'Group';
		$userId = $controller->activeUser['User']['id'];
		$authHistory = $controller->Session->read('CoreDebugPanels.authHistory');

		if (!isset($controller->activeUser['ConditionalGroup'])) {
			$controller->_setConditionalGroups();

			// check for conditional group, which takes priority
			if (isset($controller->activeUser['ConditionalGroup'])) {
				$foreign_key = $controller->activeUser['ConditionalGroup']['id'];
				$condAccess = $controller->Acl->check(compact('model', 'foreign_key'), $controller->Auth->action());
				$action = $controller->Auth->action();
				$perm = $condAccess ? 'yes' : 'no';
				$message = "User $userId of conditional group $foreign_key allowed to access $action? [$perm]";
				$authHistory[] = $message;
				if ($this->log) {
					CakeLog::write('auth', $message);
				}
			}
		}

		$foreign_key = $controller->activeUser['Group']['id'];
		$mainAccess = $controller->Acl->check(compact('model', 'foreign_key'), $controller->Auth->action());
		$action = $controller->Auth->action();
		$perm = $mainAccess ? 'yes' : 'no';
		$message = "User $userId of group $foreign_key allowed to access $action? [$perm]";
		$authHistory[] = $message;
		if ($this->log) {
			CakeLog::write('auth', $message);
		}

		if ($this->history > 0) {
			while (count($authHistory) > $this->history) {
					array_shift($authHistory);
			}
		}

		$controller->Session->write('CoreDebugPanels.authHistory', $authHistory);
	}

}

?>
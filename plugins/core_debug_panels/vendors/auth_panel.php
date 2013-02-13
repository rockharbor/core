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
 * Should we log accepted requests in addition to keeping them in the session?
 *
 * @var boolean
 */
	var $logAllow = true;

/*
 * Should we log denied requests in addition to keeping them in the session?
 *
 * @var boolean
 */
	var $logDeny = true;

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
		$groupId = $controller->activeUser['Group']['id'];
		$controller->_setConditionalGroups();
		$authHistory = $controller->Session->read('CoreDebugPanels.authHistory');
		$action = $controller->Auth->action();

		$condAccess = false;
		if (!empty($controller->activeUser['ConditionalGroup'])) {
			foreach ($controller->activeUser['ConditionalGroup'] as $group) {
				$condAccess = Core::acl($groupId, $action, 'conditional');
				$message = "User of group $groupId allowed to access $action? [$condAccess]";
				$authHistory[] = $message;
				if (($this->logAllow && $condAccess) || ($this->logDeny && !$condAccess)) {
					CakeLog::write('auth', $message);
				}
				if ($condAccess) {
					break;
				}
			}
		}

		// main group
		$mainAccess = Core::acl($groupId, $action);
		$message = "User of group $groupId allowed to access $action? [$mainAccess]";
		$authHistory[] = $message;
		if (($this->logAllow && $mainAccess) || ($this->logDeny && !$mainAccess)) {
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


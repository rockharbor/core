<?php

class AuthGroupsController extends AppController {

	var $uses = array('Group');

	function beforeFilter() {
		parent::beforeFilter();
	}

	function swap($groupId = null) {
		if (Configure::read() < 2 || !$groupId || !$this->activeUser) {
			return;
		}

		$group = $this->Group->read(null, $groupId);

		// save applicable areas
		$this->Session->write('User.Group', reset($group));
		$this->Session->write('User.User.group_id', $group['Group']['id']);
		$this->Session->write('Auth.User.group_id', $group['Group']['id']);

		$this->activeUser['Group'] = reset($group);
		$this->activeUser['User']['group_id'] = $group['Group']['id'];
		
		$this->set('activeUser', $this->activeUser);
	}
}

?>

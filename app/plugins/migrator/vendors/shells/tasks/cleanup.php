<?php

class CleanupTask extends MigratorShell {

/**
 * Handles the things that we couldn't do during migration
 */
	function migrate() {
		$this->out('Cleaning up...');

		// set ministry tree
		$this->out('Recovering Ministry tree...');
		$this->Ministry = ClassRegistry::init('Ministry');
		$this->Ministry->Behaviors->attach('Tree');
		$this->Ministry->recover();

		$this->out('Setting entered by id');
		$this->User = ClassRegistry::init('User');
		$data = $this->User->find('all', array(
			'contain' => array(
				'Profile' => array(
					'fields' => array('id', 'user_id', 'created_by')
				)
			)
		));
		foreach($data as $user) {
			$link = $this->IdLinkage->find('first', array(
				'conditions' => array(
					'new_model' => 'User',
					'old_table' => 'person',
					'old_pk' => $user['Profile']['created_by']
				)
			));
			if ($link === false || empty($link)) {
				CakeLog::write('migration', 'During cleanup, unable to find person # '.$user['Profile']['created_by']);
				$link['IdLinkage']['new_pk'] = '';
			}
			$this->User->Profile->id = $user['Profile']['id'];
			$this->User->Profile->saveField('created_by', $link['IdLinkage']['new_pk']);
		}
	}


}
?>

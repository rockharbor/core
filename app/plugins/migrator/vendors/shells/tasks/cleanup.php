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
	}


}
?>

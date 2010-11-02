<?php

Configure::write('Cache.disable', false);
require_once 'tasks'.DS.'migrator.php';

class MigratorShell extends Shell {

/**
 * Tasks should be in the ORDER they are to be performed! 
 *
 * @var array
 */
	var $tasks = array(
		'School',
		'JobCategory',
		'User',
		'Household',
		'HouseholdMember',
		'StaffComment',
		'PastoralComment',
		'Address',
		'Ministry',
		'Event',
		'Date',
		'Group',
		'Team',
		'Manager',
		'EventLeader',
		'GroupLeader',
		'TeamLeader',
		'PaymentOption',
		'Question',
		'EventRoster',
		'TeamRoster',
		'GroupRoster',
		'Role',
		'RolesRoster',
		'Image',
		'Document',
		'Payment',
		'PaymentRoster',
		'Publication',
		'Subscription',
		'Region',
		'Zipcode',
		'Cleanup',
	);

	var $_oldDbConfig = 'old';

	function migrate_database() {
		ini_set('memory_limit', '256M');

		$this->_createLinkageTable();
		unlink(TMP.'logs'.DS.'migration.log');
		$start = microtime(true);
		if (!empty($this->args[0]) && isset($this->{$this->args[0]})) {
			$limit = null;
			if (!empty($this->args[1])) {
				$limit = $this->args[1];
			}
			$this->{$this->args[0]}->IdLinkage = ClassRegistry::init('IdLinkage');
			$this->{$this->args[0]}->migrate($limit);
		} elseif (empty($this->args[0])) {
			foreach ($this->tasks as $task) {
				$this->{$task}->IdLinkage = ClassRegistry::init('IdLinkage');
				$this->{$task}->migrate();
			}
		} else {
			$this->out($this->args[0].' task isn\'t attached.');
		}

		CakeLog::write('migration', 'Total migration time: '.(microtime(true)-$start).' seconds');
		$this->out('Migration complete!');
	}

	function _createLinkageTable() {
		$ds = ConnectionManager::getDataSource('default');
		$ds->execute('
		CREATE TABLE IF NOT EXISTS `id_linkages` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `old_pk` varchar(100) DEFAULT NULL,
		  `old_table` varchar(100) DEFAULT NULL,
		  `new_model` varchar(100) DEFAULT NULL,
		  `new_pk` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `index_2` (`old_pk`,`old_table`,`new_model`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1
		');
	}

}
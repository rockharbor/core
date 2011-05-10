<?php

class SubscriptionTask extends MigratorTask {

	var $_oldPkMapping = array(
		'person_id' => array('person' => 'User'),
		'publication_id' => array('publication' => 'Publication'),
	);

	var $_oldTable = 'publication_subscriptions';
	var $_oldPk = 'publication_subscription_id';
	var $_newModel = 'PublicationsUser';

	function mapData() {
		$this->_editingRecord = array(
			'PublicationsUser' => array(
				'publication_id' => $this->_editingRecord['publication_id'],
				'user_id' => $this->_editingRecord['person_id'],
			)
		);
	}

}
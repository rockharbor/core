<?php
/* MergeRequest Fixture generated on: 2010-06-28 09:06:06 : 1277741346 */
class MergeRequestFixture extends CakeTestFixture {
	var $name = 'MergeRequest';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'merge_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'requester_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
	);
}
?>
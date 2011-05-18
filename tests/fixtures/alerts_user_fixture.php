<?php
/* AlertsUser Fixture generated on: 2010-06-30 07:06:51 : 1277908431 */
class AlertsUserFixture extends CakeTestFixture {
	var $name = 'AlertsUser';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'alert_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join' => array('column' => array('alert_id', 'user_id'), 'unique' => 1), 'alert_key' => array('column' => 'alert_id', 'unique' => 0), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'alert_id' => 1,
			'user_id' => 1
		)
	);
}
?>
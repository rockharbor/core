<?php
/**
 * Queues controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       queue_email
 * @subpackage    queue_email.config.schema
 */

/**
 * Queues Controller
 *
 * @package       queue_email
 * @subpackage    queue_email.config.schema
 */
class QueueSchema extends CakeSchema {

	var $name = 'Queue';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $queues = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'to' => array('type' => 'text', 'default' => NULL),
		'cc' => array('type' => 'text', 'default' => NULL),
		'bcc' => array('type' => 'text', 'default' => NULL),
		'from' => array('type' => 'string', 'default' => NULL),
		'subject' => array('type' => 'string', 'default' => NULL),
		'delivery' => array('type' => 'string', 'default' => NULL, 'length' => 4),
		'smtp_options' => array('type' => 'text', 'default' => NULL),
		'message' => array('type' => 'text', 'text' => NULL),
		'header' => array('type' => 'text', 'text' => NULL),
		'created' => array('type' => 'datetime', 'default' => NULL),
		'modified' => array('type' => 'datetime', 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

}

?>
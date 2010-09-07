<?php
/**
 * Sys email model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * SysEmail model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class SysEmail extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'SysEmail';

/**
 * The table to use, or false for none
 *
 * @var boolean
 */
	var $useTable = false;

/**
 * Manually defined schema for validation
 *
 * @var array
 */
	var $_schema = array(
		'subject' => array(
			'type' => 'string',
			'length' => 45
		),
		'body' => array(
			'type' => 'text'
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'subject' => array(
			'rule' => 'notempty',
			'required' => true
		),
		'body' => array(
			'rule' => 'notempty',
			'required' => true
		)
	);

/**
 * Sanitization rules
 *
 * @var array
 * @see Sanitizer.SanitizeBehavior
 */
	var $sanitize = array(
		'body' => 'html'
	);
	
/**
 * Overwrite Model::exists() due to Cake looking for a table when validating.
 *
 * @return boolean True
 */
	function exists() {
		return true;
	}

	
/**
 * Garbage collects email attachments
 *
 * Deletes all attachments that are older than 1 day. Or, if $uid is defined,
 * it will clear out attachments associated with that id.
 *
 * @param string $uid A foreign_key to look for
 * @return boolean True on success, false on failure
 */
	function gcAttachments($uid = null) {
		// load documents
		$Document = ClassRegistry::init('Document');
		// Model::deleteAll() currently doesn't honor recursive (cake ticket #561)
		$Document->recursive = -1;
		if (!$uid) {
			// delete all attachments that don't have a cache file associated
			return $Document->deleteAll(array(
				'Document.model' => 'SysEmail',
				'Document.created <' => date('Y-m-d')
			));
		} else {
			return $Document->deleteAll(array(
				'Document.foreign_key' => $uid,
				'Document.model' => 'SysEmail'
			));
		}
	}
}
?>
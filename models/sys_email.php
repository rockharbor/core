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
	public $name = 'SysEmail';

/**
 * The table to use, or false for none
 *
 * @var boolean
 */
	public $useTable = 'queues';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'subject' => array(
			'rule' => 'notempty',
			'required' => true,
			'message' => 'Please fill in the required field.'
		),
		'body' => array(
			'rule' => 'notempty',
			'required' => true,
			'message' => 'Please fill in the required field.'
		)
	);

/**
 * Sanitization rules
 *
 * @var array
 * @see Sanitizer.SanitizeBehavior
 */
	public $sanitize = array(
		'body' => 'stripScripts'
	);

/**
 * BelongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ToUser' => array(
			'className' => 'User',
			'foreignKey' => 'to_id'
		),
		'FromUser' => array(
			'className' => 'User',
			'foreignKey' => 'from_id'
		)
	);

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Containable'
	);

/**
 * Caculates pagination count
 *
 * @param array $conditions
 * @param array $recursive
 * @param array $options
 * @return integer
 */
	public function paginateCount($conditions, $recursive, $options) {
		$options += array(
			'fields' => array(
				'SysEmail.from_id'
			),
			'conditions' => $conditions
		);
		$count = $this->find('all', $options);
		return count($count);
	}

/**
 * Garbage collects email attachments
 *
 * Deletes all attachments that are older than 1 day (orphaned). Or, if $uid is
 * defined, it will clear out attachments associated with that id.
 *
 * @param string $uid A foreign_key to look for
 */
	public function gcAttachments($uid = null) {
		// load documents
		$Document = ClassRegistry::init('Document');
		$Document->recursive = -1;

		if (!$uid) {
			// delete all attachments that don't have a cache file associated
			$results =  $Document->find('all', array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'Document.model' => 'SysEmail',
					'Document.created <' => date('Y-m-d')
				)
			));
		} else {
			$results =  $Document->find('all', array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'Document.foreign_key' => $uid,
					'Document.model' => 'SysEmail'
				)
			));
		}
		// iterate through each one so callbacks are called (specifically, so Media
		// plugin removes the files)
		foreach ($results as $result) {
			$Document->delete($result['Document']['id']);
		}
	}
}

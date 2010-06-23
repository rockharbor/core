<?php
/**
 * Document model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Includes
 */
App::import('Model', 'Media.MediaApp');

/**
 * Document model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Document extends MediaAppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Document';

/**
 * The table to use, or false for none
 *
 * @var boolean
 */
	var $useTable = 'attachments';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Media.Transfer' => array(
			'trustClient' => false,
			'transferDirectory' => MEDIA_TRANSFER,
			'createDirectory' => true,
			'alternativeFile' => 100
		),
		'Media.Generator' => array(
			'baseDirectory' => MEDIA_TRANSFER,
			'filterDirectory' => MEDIA_FILTER,
			'createDirectory' => true,
		),
		'Media.Polymorphic',
		'Media.Coupler' => array(
			'baseDirectory' => MEDIA_TRANSFER
		),
		'Logable'
	);

/**
 * Validation rules for file and alternative fields
 *
 * For more information on the rules used here see the source of
 * TransferBehavior and MediaBehavior or the test case for MediaValidation.
 *
 * If you experience problems with your model not validating, try commenting
 * the mimeType rule or providing less strict settings for single rules.
 *
 * `checkExtension()` and `checkMimeType()` take both a blacklist and a
 * whitelist. If you are on windows make sure that you addtionally specify the
 * `'tmp'` extension in case you are using a whitelist.
 *
 * @var array
 */
	var $validate = array(
		'file' => array(
			'resource' => array(
				'rule' => 'checkResource',
				'message' => 'Invalid resource.'
			),
			'access' => array(
				'rule' => 'checkAccess',
				'message' => 'Cannot access.'
			),
			'location' => array(
				'rule' => array('checkLocation', array(
					MEDIA_TRANSFER, '/tmp/', 'http://', 'C:\\'
				)),
				'message' => 'Invalid upload location.'
			),
			'permission' => array(
				'rule' => array('checkPermission', '*'),
				'message' => 'Problem with permissions.'
			),
			'size'       => array(
				'rule' => array('checkSize', '5M'),
				'message' => 'Document must be less than 5Mb.'
			),
			'extension'  => array(
				'rule' => array(
					'checkExtension', 
					false, 
					array(
						'txt', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'tmp'
					)
				),
				'message' => 'Invalid file type.'
			)
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'User' => array(
			'foreignKey' => 'foreign_key'	
		)
	);
		
/**
 * Generate a version of a file
 *
 * Uncomment to force Generator Behavior to use this method when
 * generating versions of files.
 *
 * If you want to fall back from your code back to the default method use:
 * `return $this->Behaviors->Generator->makeVersion($this, $file, $process);`
 *
 * $process an array with the following contents:
 *  directory - The destination directory (If this method was called
 *              by `make()` the directory is already created)
 *  version - The version requested to be processed (e.g. `l`)
 *  instructions - An array containing which names of methods to be called
 *
 * @param file $file Absolute path to source file
 * @param array $process version, directory, instructions
 * @return boolean `true` if version for the file was successfully stored
 */
	// function makeVersion($file, $process) {
	// }

/**
 * Returns the relative path to the destination file
 *
 * Uncomment to force Transfer Behavior to use this method when
 * determining the destination path instead of the builtin one.
 *
 * @param array $via Information about the temporary file
 * @param array $from Information about the source file
 * @return string The path to the destination file or false
 */
	// function transferTo($via, $from) {
	// }
}
?>
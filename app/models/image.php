<?php

App::import('Model', 'Media.MediaApp');

class Image extends MediaAppModel {

/**
 * Name of model
 *
 * @var string
 * @access public
 */
	var $name = 'Image';

/**
 * Name of table to use
 *
 * @var mixed
 * @access public
 */
	var $useTable = 'attachments';

/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'Media.Transfer' => array(
			'trustClient' => false,
			'baseDirectory' => MEDIA_TRANSFER,
			'createDirectory' => true,
			'alternativeFile' => 100
		),
		'Media.Polymorphic',
		'Logable'
	);

/**
 * Validation rules for file and alternative fields
 *
 * For more information on the rules used here
 * see the source of TransferBehavior and MediaBehavior or
 * the test case for MediaValidation.
 *
 * If you experience problems with your model not validating,
 * try commenting the mimeType rule or providing less strict
 * settings for single rules.
 *
 * `checkExtension()` and `checkMimeType()` take both a blacklist and
 * a whitelist. If you are on windows make sure that you addtionally
 * specify the `'tmp'` extension in case you are using a whitelist.
 *
 * @var array
 * @access public
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
			'permission' => array('rule' => array('checkPermission', '*')),
			'size'       => array(
				'rule' => array('checkSize', '5M'),
				'message' => 'Image must be less than 5Mb.'
			),
			'extension'  => array(
				'rule' => array(
					'checkExtension', 
					false, 
					array(
						'png', 'jpg', 'gif', 'tmp'
					)
				),
				'message' => 'Invalid file type.'
			)
		)
	);

	var $belongsTo = array(
		'User' => array(
			'foreignKey' => 'foreign_key'	
		)
	);
		
		
		
	function beforeSave() {		
		// has many
		if (!empty($this->data)) {
			$document['Document']['group'] = 'Image';
		}
		
		return true;
	}
		
/**
 * beforeMake Callback
 *
 * Called from within `MediaBehavior::make()`
 *
 * $process an array with the following contents:
 *	overwrite - If the destination file should be overwritten if it exists
 *	directory - The destination directory (guranteed to exist)
 *  name - Media name of $file (e.g. `'Image'`)
 *	version - The version requested to be processed (e.g. `'xl'`)
 *	instructions - An array containing which names of methods to be called
 *
 * @param string $file Absolute path to the source file
 * @param array $process directory, version, name, instructions, overwrite
 * @access public
 * @return boolean True signals that the file has been processed,
 * 	false or null signals that the behavior should process the file
 */
	// function beforeMake($file, $process) {
	// }

/**
 * Returns the relative path to the destination file
 *
 * @param array $via Information about the temporary file
 * @param array $from Information about the source file
 * @return string The path to the destination file or false
 */
	// function transferTo($via, $from) {
	// }
}
?>
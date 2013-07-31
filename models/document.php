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
	public $name = 'Document';

/**
 * The table to use, or false for none
 *
 * @var boolean
 */
	public $useTable = 'attachments';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Media.Transfer' => array(
			'trustClient' => false,
			'transferDirectory' => MEDIA_TRANSFER,
			'createDirectory' => true,
			'alternativeFile' => 100
		),
		'Media.Polymorphic',
		'Media.Coupler' => array(
			'baseDirectory' => MEDIA_TRANSFER
		),
		'Logable',
	);

/**
 * Sanitization rules for this model
 *
 * @var array
 * @see Sanitizer.Sanitize
 */
	public $sanitize = array(
		'file' => false
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
	public $validate = array(
		'file' => array(
			'resource' => array(
				'rule' => 'checkResource',
				'message' => 'Upload failed. Please try again.'
			),
			'access' => array(
				'rule' => 'checkAccess',
				'message' => 'Upload failed. Please try again.'
			),
			'permission' => array(
				'rule' => array('checkPermission', '*'),
				'message' => 'Upload failed. Please try again.'
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
 * Returns the relative path to the destination file
 *
 * @param array $via Information about the temporary file
 * @param array $from Information about the source file
 * @return string The path to the destination file or false
 */
	public function transferTo($via, $from) {
		extract($from);

		$irregular = array(
			'image' => 'img',
			'text' => 'txt'
		);
		$name = Mime_Type::guessName($mimeType ? $mimeType : $file);
		if (empty($extension)) {
			$extension = Mime_Type::guessExtension($mimeType ? $mimeType : $file);
		}

		if (isset($irregular[$name])) {
			$short = $irregular[$name];
		} else {
			$short = substr($name, 0, 3);
		}

		$path  = $short . DS;
		$path .= strtolower(Inflector::underscore($this->model)) . DS;
		$path .= String::uuid();
		$path .= !empty($extension) ? '.' . strtolower($extension) : null;

		return $path;
	}
}

<?php
/**
 * Image model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Image model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Image extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Image';

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
		'Logable',
		'NamedScope.NamedScope' => array(
			'promoted' => array(
				'conditions' => array(
					'promoted' => true,
					'approved' => true,
					'group' => 'Image',
					'model' => array('Involvement', 'Ministry')
				),
				'limit' => 2
			)
		)
	);

/**
 * Sanitization rules for this model
 *
 * @var array
 * @see Sanitizer.Sanitize
 */
	var $sanitize = array(
		'file' => false
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

/**
 * Runs before saving and generating the images. Sets the filters from the defaults
 * defined in `/config/bootstrap.php` based on the model.
 *
 * @return boolean True to continue saving
 */
	function beforeSave() {
		if (isset($this->data[$this->alias])) {
			$data = $this->data[$this->alias];
		} else {
			$data = $this->data;
		}
		$model = isset($data['model']) ? $data['model'] : null;
		if (!Configure::read('Core.mediafilters.'.strtolower($model))) {
			$model = 'default';
		}
		Configure::write('Media.filter.image', Configure::read('Core.mediafilters.'.strtolower($model)));
		return true;
	}

/**
 * Returns the relative path to the destination file
 *
 * @param array $via Information about the temporary file
 * @param array $from Information about the source file
 * @return string The path to the destination file or false
 */
	function transferTo($via, $from) {
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
?>
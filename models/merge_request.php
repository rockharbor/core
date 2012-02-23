<?php
/**
 * Merge request model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * MergeRequest model
 *
 * Polymorphic model
 *
 * By default, this model assumes you are merging Users and will find the data
 * based on that assumption. To merge different models, change the `className`
 * keys in the `Source` and `Target` keys in MergeRequest::belongsTo
 *
 * @package       core
 * @subpackage    core.app.models
 */
class MergeRequest extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'MergeRequest';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable',
		'Logable'
	);

/**
 * BelongsTo association link
 *
 * Note: For Source and Target, className and conditions must be redefined before a find!
 *
 * @var array
 */
	var $belongsTo = array(
		'Requester' => array(
			'className' => 'User',
			'foreignKey' => 'requester_id',
			'dependent' => false
		),
		'NewModel' => array(
			'className' => 'User',
			'foreignKey' => 'model_id',
			'dependent' => false
		),
		'OriginalModel' => array(
			'className' => 'User',
			'foreignKey' => 'merge_id',
			'dependent' => false
		)
	);

}
?>
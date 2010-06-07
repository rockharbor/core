<?php

class MergeRequest extends AppModel {
	
	var $name = 'MergeRequest';
	
	var $actsAs = array(
		'Logable'
	);
	
/**
 * Model::belongsTo associations
 *
 * Note: For Source and Target, className and conditions must be redefined before a find!
 */
	var $belongsTo = array(
		'Requester' => array(
			'className' => 'User',
			'foreignKey' => 'requester_id',
			'dependent' => false
		),
		'Source' => array(
			'className' => 'User',
			'foreignKey' => 'model_id',
			'dependent' => false
		),
		'Target' => array(
			'className' => 'User',
			'foreignKey' => 'merge_id',
			'dependent' => false
		)
	);

}

?>
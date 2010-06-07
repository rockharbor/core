<?php
class InvolvementType extends AppModel {
	var $name = 'InvolvementType';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_type_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
?>
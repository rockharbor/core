<?php
/**
 * RequestType model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       communications_requests
 * @subpackage    communications_requests.models
 */

/**
 * RequestType
 *
 * @package       communications_requests
 * @subpackage    communications_requests.models
 */
class RequestType extends CommunicationsRequestsAppModel {
	
/**
 * The name of the model
 * 
 * @var string
 */	
	var $name = 'RequestType';
	
/**
 * Default order
 * 
 * @var string
 */
	var $order = 'RequestType.name';
	
/**
 * Validation rules
 * 
 * @var array
 */	
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		)
	);
	
/**
 * Extra behaviors for this model
 * 
 * @var array
 */
	var $actsAs = array(
		'Containable'
	);

/**
 * HasMany association link
 * 
 * @var array 
 */
	var $hasMany = array(
		'CommunicationsRequests.RequestNotifier'
	);

}
?>
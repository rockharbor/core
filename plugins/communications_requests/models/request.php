<?php
/**
 * Request model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       communications_requests
 * @subpackage    communications_requests.models
 */

/**
 * Request
 *
 * @package       communications_requests
 * @subpackage    communications_requests.models
 */
class Request extends CommunicationsRequestsAppModel {
	
/**
 * The name of the model
 * 
 * @var string
 */	
	var $name = 'Request';
	
/**
 * Default order
 * 
 * @var string
 */
	var $order = 'Request.created DESC';
	
/**
 * Validation rules
 * 
 * @var array
 */	
	var $validate = array(
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'ministry_name' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'budget' => array(
			'money' => array(
				'rule' => array('money'),
				'allowEmpty' => true
			)
		)
	);
	
/**
 * Extra behaviors for this model
 * 
 * @var array
 */
	var $actsAs = array(
		'Containable',
		'Linkable.AdvancedLinkable'
	);

/**
 * BelongsTo assocation link
 * 
 * @var array
 */
	var $belongsTo = array(
		'User',
		'CommunicationsRequests.RequestType',
		'CommunicationsRequests.RequestStatus',
		'Involvement'
	);

}
?>
<?php
/**
 * RequestNotifier model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       communications_requests
 * @subpackage    communications_requests.models
 */

/**
 * RequestNotifier
 *
 * @package       communications_requests
 * @subpackage    communications_requests.models
 */
class RequestNotifier extends CommunicationsRequestsAppModel {
	
/**
 * The name of the model
 * 
 * @var string
 */	
	var $name = 'RequestNotifier';

/**
 * Extra behaviors for this model
 * 
 * @var array
 */
	var $actsAs = array(
		'Containable'
	);

/**
 * BelongsTo assocation link
 * 
 * @var array
 */
	var $belongsTo = array(
		'User',
		'CommunicationsRequests.RequestType'
	);

}
?>
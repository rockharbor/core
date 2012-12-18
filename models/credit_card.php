<?php
/**
 * Credit card model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * CreditCard model
 *
 * When data is saved, it is validated and processed by Authorize.net instead of
 * being stored in the database.
 *
 * @package       core
 * @subpackage    core.app.models
 * @todo Save should record a payment as well
 */
class CreditCard extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'CreditCard';

/**
 * The table to use, or false for none
 *
 * @var boolean
 */
	var $useTable = false;

/**
 * Manually defined schema for validation
 *
 * @var array
 */
	var $_schema = array(
		'credit_card_number' => array(
			'type' => 'integer',
			'length' => 16
		),
		'cvv' => array(
			'type' => 'integer',
			'length' => 4
		),
		'expiration_date' => array(
			'type' => 'date'
		),
		'first_name' => array(
			'type' => 'string',
			'length' => 45
		),
		'last_name' => array(
			'type' => 'string',
			'length' => 45
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'first_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'email' => array(
			'notEmpty' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'credit_card_number' => array(
			'cc' => array(
				'rule' => array('cc', 'all', false, null),
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'cvv' => array(
			'minLength' => array(
				'rule' => array('minLength', 3),
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'amount' => array(
			'money' => array(
				'rule' => 'money',
				'required' => true,
				'message' => 'Please enter a valid amount.'
			)
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'invoice_number' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'address_line_1' => array(
			'rule' => 'notEmpty',
			'message' => 'Please fill in the required field.'
		),
		'city' => array(
			'rule' => 'notEmpty',
			'message' => 'Please fill in the required field.'
		),
		'state' => array(
			'rule' => 'notEmpty',
			'message' => 'Please fill in the required field.'
		),
		'zip' => array(
			'rule' => array('postal', null, 'us'),
			'message' => 'Please enter a valid zipcode.',
			'allowEmpty' => false
		)
	);

/**
 * The returned transaction id. Set after a saveAll call
 *
 * @var string
 * @todo Use id instead to maintain conventions
 */ 
	var $transactionId = null;
	
/**
 * The payment gateway component
 * 
 * @var AuthorizeDotNetComponent
 */
	var $gateway = null;

/**
 * Overwrite Model::exists() due to Cake looking for a table
 * when validating.
 */
	function exists() {
		return true;
	}
	
/**
 * Overwrite Model::saveAll()
 *
 * @param array $data The data to process
 * @param array $options Save options (currently only supports `validate`)
 * @return boolean Success
 */
	function saveAll($data, $options = array()) {
		$continue = true;
		foreach ($data[$this->alias] as $creditCard) {
			if ($continue) {
				$continue = $this->save($creditCard, $options);
			}
		}
		return $continue;
	}
	
/**
 * Overwrite Model::save() to process the card instead
 *
 * @param array $data The data to process
 * @param array $options Save options (currently only supports `validate`)
 * @return boolean Success
 */	
	function save($data, $options = array()) {
		// move to root of $data instead
		if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}
		
		$_defaults = array(
			'validate' => 'first'
		);
		$options = array_merge($_defaults, $options);
	
		$this->set($data);
		if (!($options['validate']) || $this->validates()) {
			$gateway = $this->getGateway();
			// set up credit card authorization
			$gateway->setCustomer($data);
			$gateway->setInvoiceNumber($data['invoice_number']);
			$gateway->setDescription($data['description']);
			$gateway->setAmount($data['amount']);
			
			if ($options['validate'] != 'only') {
				// run credit card
				$success = $gateway->request();
				$this->transactionId = $gateway->transactionId;
				if (!$success) {
					$this->invalidate('credit_card_number', $gateway->error);
				}
				return $success;
			}
			// just validate and it validated, so pass true
			return true;
		}
		
		return false;
	}

/**
 * Gets the gateway component
 * 
 * @return AuthorizeDotNetComponent
 */
	function getGateway() {
		if ($this->gateway === null) {
			App::import('Component', 'AuthorizeDotNet');
			$this->gateway = new AuthorizeDotNetComponent();
		}
		return $this->gateway;
	}
}

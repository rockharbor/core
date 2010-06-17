<?php
/**
 * Authorize.net component class.
 *
 * This was taken from CORE 1.0 and has been since cleaned up, documented and modified.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers.components
 */

/**
 * AuthorizeDotNet Component
 *
 * ### Test card numbers (use any expiry date):
 * - 370000000000002 - American Express Test Card
 * - 6011000000000012 - Discover Test Card
 * - 5424000000000015 - MasterCard Test Card
 * - 4007000000027 - Visa Test Card
 * - 4012888818888 - Visa Test Card II
 *
 * @package       core
 * @subpackage    core.app.controllers.components
 * @link http://www.authorize.net/support/AIM_guide.pdf
 * @todo Move into a behavior? Or a vendor?
 */
class AuthorizeDotNetComponent extends Object {

/**
 * Authorize.net login credentials
 *
 * @var array
 * @access protected
 */
	var $_credentials = array(
		'username' 	=> '7u9e6TuTw',        // authorize.net username
		'password' 	=> '77bX8977DquU6LE4',    // authorize.net password
		'useragent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)',    // browser to identify ourselves as
		'refer'		=> ''
	);

/**
 * Data to send to authorize.net
 *
 * @var array
 * @access protected
 */
	var $_data = array();

/**
 * Error message, if any
 *
 * @var string
 */ 
	var $error = '';

/**
 * Transaction id
 *
 * @var string
 */ 	
	var $transactionId = '';
	
/**
 * Reference to controller
 *
 * @var object
 */
	var $controller = null;	
	
/**
 * Start AuthorizeDotNetComponent for use in the controller
 *
 * @param object $controller A reference to the controller
 * @access public
 */
	function initialize(&$controller = null) {
		$this->controller =& $controller;
		
		// check debug
		if (Configure::read('debug') > 0) {
			$email = $this->controller->CORE['settings']['debug_email'];
			$this->_data['x_Test_Request'] = 'TRUE';
		} else {
			$email = $this->controller->CORE['settings']['credit_card_email'];
			$this->_data['x_Test_Request'] = 'FALSE';
		}
		
		// referrer url to report to authorize.net
		$this->_credentials['refer'] = $_SERVER['REQUEST_URI'];

		/* Sets the Authorize.net account info */
		$this->_data['x_Login'] = $this->_credentials['username'];
		$this->_data['x_Password'] = $this->_credentials['password'];

		/* Sets preferences - return info should be pipe (|) delimited */
		$this->_data['x_Delim_Data'] = 'TRUE';
		$this->_data['x_Delim_Char'] = '|';
		$this->_data['x_Encap_Char'] = '';
					
		/* Confirmation Emails - should the customer get one? where should the merchant's copy be sent? */
		$this->_data['x_Email_Customer'] = 'FALSE';
			
		$this->_data['x_Merchant_Email'] = $email; // optional, comment this line to suppress the merchant copy
					
		/* Set transaction type */
		$this->_data['x_Type'] = 'AUTH_CAPTURE';
		$this->_data['x_Method'] = 'CC';
					
		/* A required field, but the only valid value is "FALSE" */
		$this->_data['x_ADC_Relay_Response'] = 'FALSE';

		/* API version we're using */
		$this->_data['x_Version'] = '3.1';
	}

/**
 * Sets the invoice title
 *
 * @param string $str
 */
	function setInvoice($str) { 
		$this->_data['x_Invoice'] = $str; 
	}

/**
 * Sets the amount to charge
 *
 * @param float $amount
 */
	function setAmount($amount) {
		$amount = number_format($amount,2,".","");
		$this->_data['x_Amount'] = $amount;
	}

/**
 * Sets customer data
 *
 * @param array $customer
 * @access public
 */ 
	function setCustomer($customer = array()) {		
		// customer's info
		$this->_data['x_First_Name'] = $customer['first_name'];
		$this->_data['x_Last_Name'] = $customer['last_name'];
		$this->_data['x_Card_Num'] = $customer['credit_card_number'];
		$this->_data['x_card_code'] = $customer['cvv'];
		$this->_data['x_Exp_Date'] = $customer['expiration_date']['month'] . $customer['expiration_date']['year'];
		
		$this->_data['x_Address'] = $customer['address_line_1'].' '.$customer['address_line_2'];
		$this->_data['x_City'] = $customer['city'];
		$this->_data['x_State'] = $customer['state'];
		$this->_data['x_Zip'] = $customer['zip'];
		$this->_data['x_Email'] = $customer['email'];
	}

/**
 * Sets the invoice number
 *
 * @param integer $invoiceNumber
 */
	function setInvoiceNumber($invoiceNumber) {
		// according to the documentation, invoice number can only be 20 characters, no symbols
		$this->_data['x_invoice_num'] = substr(preg_replace("/[^-a-zA-Z0-9]/", '', $invoiceNumber), 0, 20); 
	}

/**
 * Sets the description
 *
 * @param string $desc
 */
	function setDescription($desc) {
		// according to the documentation, invoice number can only be
		// 255 characters, no symbols. helpfully, they don't tell us what
		// they define to be a "symbol".
		$desc = trim($desc);
		$desc = preg_replace("/[^-a-zA-Z0-9. '\"\/\[\]!@#$%&*\(\)_+=<>?\:\/]/", '', $desc);
		$this->_data['x_description'] = substr($desc, 0, 255);
	}
			
/**
 * Sends the request to authorize.net
 *
 * @todo Update to use HTTPSocket class instead of manual curl
 * @return boolean Success
 */
	function request() {	
		$fields = array();
		
		// Build the data string that we're posting
		foreach ($this->_data as $key => $value) {
			$fields[] = $key . '=' . urlencode(trim($value));
		}
		
		$fields = implode('&', $fields);

		/* Set up CURL to post the $fields string to authorize.net */
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://secure.authorize.net/gateway/transact.dll");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,0);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->_credentials['useragent']);
		curl_setopt($ch, CURLOPT_REFERER, $this->_credentials['refer']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
					
		/* we set CURL to return the response, so we capture it in $buffer */
		$buffer = curl_exec($ch);
		curl_close($ch);
					
		/* return values are comma delimited, as specified by x_Delim_Char */
		$details = explode($this->_data['x_Delim_Char'], $buffer);  	// $details = explode(",",$buffer);

		$this->error = $details[3];
		$this->transactionId = $details[6];
					
		/* authorize.net returns a 1 on success. */
		return ($details[0] == '1');
	}
}

?>
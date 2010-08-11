<?php
/**
 * Address controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Addresses Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class AddressesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Addresses';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('SelectOptions');

/**
 * The name of the model this Address belongs to. Used for Acl
 *
 * @var string
 */
	var $model = null;

/**
 * The id of the model this Address belongs to. Used for Acl
 *
 * @var integer
 */
	var $modelId = null;

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Model::beforeRender() callback
 */
	function beforeRender() {
		parent::beforeRender();
	
		$this->set('model', $this->model);
		$this->set('modelId', $this->modelId);
	}
	
/**
 * Shows a list of addresses
 */
	function index() {
		$this->set('data', $this->paginate('Address', array(
			'foreign_key' => $this->modelId,
			'model' => $this->model
		)));
	}

/**
 * Adds an address
 *
 * By default, the newly created address is set as the new primary address. All other addresses
 * belonging to this model are marked as not being primary
 */
	function add() {		
		if (!empty($this->data)) {
			// check to see if they chose an existing address. 
			// if so, duplicate the selected address			
			if (isset($this->data['Address']['existing'])) {
				$selectedAddress = $this->Address->read(null, $this->data['Address']['address_id']);
				
				// not an update!
				unset($selectedAddress['Address']['id']);
				
				// new model id
				$selectedAddress['Address']['foreign_key'] = $this->modelId;
				
				// clear out old data and use the existing address instead
				$this->data = array();
				$this->data = $selectedAddress;
			}
			
			$this->Address->create();
			
			// this address will be the new primary
			$this->data['Address']['primary'] = 1;
			$success= $this->Address->save($this->data);
			
			// don't overwrite that we just made it primary!
			$lastId = $this->Address->getLastInsertID();
			
			// mark all others as not primary
			if ($success) {
				$this->Address->updateAll(
					array(
						'Address.primary' => 0
					),
					array(
						'Address.foreign_key' => $this->data['Address']['foreign_key'],
						'Address.model' => $this->data['Address']['model'],
						'Address.id <>' => $lastId
					)
				);
				$this->Address->id = $lastId;
				$this->Session->setFlash('The address was saved!', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Boo! The address could not be saved.', 'flash'.DS.'failure');
			}
		}
		
		$addresses = array();
		if ($this->model != 'User') {
			$addresses = $this->Address->find('list', array(
				'conditions' => array(
					'model' => $this->model
				),
				'group' => 'name'
			));
		}
		
		$this->set('addresses', $addresses);
	}

/**
 * Edits an address for a specified model and model id. Resets all addresses to not
 * be primary
 *
 * @param integer $id The address id
 */
	function edit($id = null) {
		if (!$id && empty($this->data)) {			
			$this->Session->setFlash('Invalid address.', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {
			if ($this->Address->save($this->data)) {
				if ($this->data['Address']['primary']) {
					// mark all others as not primary
					$this->Address->updateAll(
						array(
							'Address.primary' => 0
						),
						array(
							'Address.foreign_key' => $this->data['Address']['foreign_key'],
							'Address.model' => $this->data['Address']['model'],
							'Address.id <>' => $id
						)
					);
				}
				$this->Address->id = $id;
				$this->Session->setFlash('The address was saved!', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Boo! The address could not be saved.', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Address->read(null, $id);
		}
	}

/**
 * Deletes an address
 *
 * @param integer $id The address id
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for address', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Address->delete($id)) {
			$this->Session->setFlash('Address deleted', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Address was not deleted', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>
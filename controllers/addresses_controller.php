<?php
class AddressesController extends AppController {

	var $name = 'Addresses';
	
	var $helpers = array('SelectOptions');
	
	var $model = null;
	var $modelId = null;

/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}
	
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
				$modelAddresses = $this->Address->find('all', array(
					'conditions' => array(
						'Address.foreign_key' => $this->data['Address']['foreign_key'],
						'Address.model' => $this->data['Address']['model'],
						'Address.id <>' => $lastId
					)
				));
				foreach ($modelAddresses as $modelAddress) {
					$this->Address->id = $modelAddress['Address']['id'];
					$this->Address->saveField('primary', 0);
				}
				
				$this->Session->setFlash('The address was saved!', 'flash_success');
			} else {
				$this->Session->setFlash('Boo! The address could not be saved.', 'flash_failure');
			}
		}
		
		$addresses = array();
		if ($model != 'User') {
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
 * Edits an address for a specified model and model id
 *
 * @param integer $id The address id
 */
	function edit($id = null) {		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid address.', 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!empty($this->data)) {
			if ($this->Address->save($this->data)) {
				// mark all others as not primary		
				$modelAddresses = $this->Address->find('all', array(
					'conditions' => array(
						'foreign_key' => $this->data['Address']['foreign_key'],
						'model' => $this->data['Address']['model'],
						'id <>' => $id
					)
				));			
				foreach ($modelAddresses as $modelAddress) {
					$this->Address->id = $modelAddress['Address']['id'];
					$this->Address->saveField('primary', 0);
				}
				
				$this->Session->setFlash('The address was saved!', 'flash_success');
			} else {
				$this->Session->setFlash('Boo! The address could not be saved.', 'flash_failure');
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
			$this->Session->setFlash('Invalid id for address', 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Address->delete($id)) {
			$this->Session->setFlash('Address deleted', 'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Address was not deleted', 'flash_failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>
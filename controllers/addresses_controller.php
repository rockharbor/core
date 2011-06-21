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
	var $helpers = array('SelectOptions', 'GoogleMap');

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
		$this->_editSelf('index', 'add', 'edit', 'toggle_activity', 'primary');
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
		$this->paginate = array(
			'conditions' => array(
				'foreign_key' => $this->modelId,
				'model' => $this->model
			),
			'order' => array(
				'primary DESC',
				'active DESC',
				'modified DESC'
			),
			'recursive' => -1
		);
		$this->set('addresses', $this->paginate());
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
				$this->Session->setFlash('Your address has been saved.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index', $this->model => $this->modelId));
			} else {
				$this->Session->setFlash('Unable to save. Please try again.', 'flash'.DS.'failure');
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
			//404
			$this->Session->setFlash('Invalid address.', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'index', $this->model => $this->modelId));
		}

		if (!empty($this->data)) {
			if ($this->Address->save($this->data)) {
				$this->Session->setFlash('Your address has been updated.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index', $this->model => $this->modelId));
			} else {
				$this->Session->setFlash('Unable to save. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Address->read(null, $id);
		}
	}

/**
 * Marks an address as primary
 *
 * @param integer $id The address id
 */
	function primary() {
		if (!$this->passedArgs['Address']) {
			//404
			$this->Session->setFlash('Invalid id for address', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index', $this->model => $this->modelId));
		}
		$related = $this->Address->related($this->passedArgs['Address']);
		$this->Address->updateAll(
			array(
				'Address.primary' => 0
			),
			array(
				'Address.id' => $related
			)
		);
		$this->Address->id = $this->passedArgs['Address'];
		$this->Address->saveField('primary', true);
		$this->Address->saveField('active', true);
		$this->Session->setFlash('Your primary address has been saved.', 'flash'.DS.'success');
		$this->redirect(array('action'=>'index', $this->model => $this->modelId));
	}

/**
 * Toggles the `active` field for an Address. You can not mark a primary Address
 * as inactive.
 *
 * @param boolean $active What to mark the `active` field
 */
	function toggle_activity($active = true) {
		if (!$this->passedArgs['Address']) {
			//404
			$this->Session->setFlash('Invalid id for address', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index', $this->model => $this->modelId));
		}
		if ($this->Address->toggleActivity($this->passedArgs['Address'], $active)) {
			$this->Session->setFlash('Your address has been '.$active ? 'activated' : 'deactivated'.'.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index', $this->model => $this->modelId));
		} else {
			$this->Session->setFlash('Unable to '.$active ? 'activate' : 'deactivate'.' your address.', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index', $this->model => $this->modelId));
		}
	}

/**
 * Deletes an address
 *
 * @param integer $id The address id
 */
	function delete($id = null) {
		if (!$id) {
			//404
			$this->Session->setFlash('Invalid id for address', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index', $this->model => $this->modelId));
		}
		if ($this->Address->delete($id)) {
			$this->Session->setFlash('Your address was deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index', $this->model => $this->modelId));
		}
		$this->Session->setFlash('Unable to delete address. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index', $this->model => $this->modelId));
	}
}
?>
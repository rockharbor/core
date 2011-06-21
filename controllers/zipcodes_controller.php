<?php
/**
 * Zipcode controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Zipcodes Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class ZipcodesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Zipcodes';
	
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
 * Adds a Zipcode to a Region
 */
	function add() {
		if (!isset($this->passedArgs['Region'])) {
			//404
			$this->setFlash('Invalid id');
			$this->redirect(array('controller' => 'regions'));
		}
				
		if (!empty($this->data)) {
			$this->Zipcode->create();
			if ($this->Zipcode->save($this->data)) {
				$this->Session->setFlash('This Zipcode has been created.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to create zipcode. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		$this->data['Zipcode']['region_id'] = $this->passedArgs['Region'];
	}
	
/**
 * Deletes a Zipcode
 *
 * @param integer $id The id of the Zipcode to delete
 */
	function delete($id = null) {
		if (!$id) {
			//404
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Zipcode->delete($id)) {
			$this->Session->setFlash('This zipcode has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete this zipcode. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}

}
?>
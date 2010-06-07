<?php


class ZipcodesController extends AppController {

	var $name = 'Zipcodes';
	
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
	
	function add() {
		if (!isset($this->passedArgs['Region'])) {
			$this->setFlash('Invalid id');
			$this->redirect(array('controller' => 'regions'));
		}
				
		if (!empty($this->data)) {
			$this->Zipcode->create();
			if ($this->Zipcode->save($this->data)) {
				$this->Session->setFlash('The Zipcode has been added', 'flash_success');
			} else {
				$this->Session->setFlash('The Zipcode could not be added. Please, try again.', 'flash_failure');
			}
		}
		
		$this->data['Zipcode']['region_id'] = $this->passedArgs['Region'];
	}
	
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Zipcode->delete($id)) {
			$this->Session->setFlash('Zipcode deleted', 'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Zipcode was not deleted', 'flash_failure');
		$this->redirect(array('action' => 'index'));
	}

}
?>
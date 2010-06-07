<?php

/**
 * Controller for simple CRUD models. 
 *
 * This simplifies adding models that use basic CRUD actions
 * without any special logic (such as categories). No need
 * to rewrite/design the same things 18 times!
 *
 * @author Jeremy Harris <jharris@rockharbor.org>
 */
class SimpleCrudsController extends AppController {

/**
 * Force the child to use this view path
 *
 * @var string viewPath
 */
	var $viewPath = 'simple_cruds';

/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		
		// for accessing the model
		$this->set('model', $this->modelClass);
		// for printing it out all friendly-like
		$this->set('modelKey', $this->modelKey);
		
		// give the views the schema, too, so they can
		// create forms with slight differences
		$schema = $this->{$this->modelClass}->schema();
		$this->set('schema', $schema);
		
		// check for dependent lists to add (i.e., ministries as a dropdown)
		foreach ($schema as $field => $attrs) {
			if (substr($field, -3) == '_id') {
				$dependentModel = Inflector::camelize(preg_replace('/_id$/', '', $field));
				$dependentListVar = Inflector::variable(
					Inflector::pluralize(preg_replace('/_id$/', '', $field))
				);
				
				$this->set($dependentListVar, $this->{$this->modelClass}->{$dependentModel}->find('list', array('fields' => array($dependentModel.'.'.$this->{$this->modelClass}->{$dependentModel}->displayField))));
			}
		}

		$this->set('title_for_layout', Inflector::pluralize(Inflector::humanize($this->modelKey)));		
	}
	
	function index() {	
		$this->{$this->modelClass}->recursive = 0;
		$this->set('results', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid '.Inflector::humanize($this->modelKey), 'flash_failure');
		}
		$this->set('result', $this->{$this->modelClass}->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->{$this->modelClass}->create();
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('The '.Inflector::humanize($this->modelKey).' has been added', 'flash_success');
				$this->redirect(array('action' => 'edit', $this->{$this->modelClass}->getInsertID()));
			} else {
				$this->Session->setFlash('The '.Inflector::humanize($this->modelKey).' could not be added. Please, try again.', 'flash_failure');
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid '.Inflector::humanize($this->modelKey), 'flash_failure');
		}
		if (!empty($this->data)) {
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('The '.Inflector::humanize($this->modelKey).' has been saved', 'flash_success');
			} else {
				$this->Session->setFlash('The '.Inflector::humanize($this->modelKey).' could not be saved. Please, try again.', 'flash_failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->{$this->modelClass}->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for '.Inflector::humanize($this->modelKey), 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->{$this->modelClass}->delete($id)) {
			$this->Session->setFlash(Inflector::humanize($this->modelKey).' deleted', 'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(Inflector::humanize($this->modelKey).' was not deleted', 'flash_failure');
		$this->redirect(array('action' => 'index'));
	}


}


?>
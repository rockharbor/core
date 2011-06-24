<?php
/**
 * Simple Crud controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Controller for simple CRUD models.
 *
 * This simplifies adding models that use basic CRUD actions
 * without any special logic (such as categories). No need
 * to rewrite/design the same things 18 times!
 *
 * @package       core
 * @subpackage    core.app.controllers
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
 * Used to override Acl permissions for this controller.
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

/**
 * Shows a list of records for this model
 */
	function index() {	
		$this->{$this->modelClass}->recursive = 0;
		$this->set('results', $this->paginate());
	}

/**
 * Shows a record for this model
 *
 * @param integer $id The id of the model
 */
	function view($id = null) {
		if (!$id) {
			//404
			$this->Session->setFlash('Invalid '.Inflector::humanize($this->modelKey), 'flash'.DS.'failure');
		}
		$this->set('result', $this->{$this->modelClass}->read(null, $id));
	}

/**
 * Adds a record for this model
 */
	function add() {
		if (!empty($this->data)) {
			$this->{$this->modelClass}->create();
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('This '.Inflector::humanize($this->modelKey).' has been created.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'edit', $this->{$this->modelClass}->getInsertID()));
			} else {
				$this->Session->setFlash('Unable to create this '.Inflector::humanize($this->modelKey).'. Please try again.', 'flash'.DS.'failure');
			}
		}
	}

/**
 * Edits a record for this model
 *
 * @param integer $id The id of the model
 */
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			//404
			$this->Session->setFlash('Invalid '.Inflector::humanize($this->modelKey), 'flash'.DS.'failure');
		}
		if (!empty($this->data)) {
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('This '.Inflector::humanize($this->modelKey).' has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to create this '.Inflector::humanize($this->modelKey).'. Please, try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->{$this->modelClass}->read(null, $id);
		}
	}

/**
 * Deletes a record for this model
 *
 * @param integer $id The id of the model
 */
	function delete($id = null) {
		if (!$id) {
			//404
			$this->Session->setFlash('Invalid id for '.Inflector::humanize($this->modelKey), 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->{$this->modelClass}->delete($id)) {
			$this->Session->setFlash('This '.Inflector::humanize($this->modelKey).' has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete this '.Inflector::humanize($this->modelKey).'.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}


}
?>
<?php
/**
 * Confirm behavior class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models.behaviors
 */

/**
 * Confirm Behavior
 *
 * Saves a revision instead of the actual record.
 *
 * @package       core
 * @subpackage    core.app.models.behaviors
 * @todo Refactor and wrap in a plugin, generally clean it up, use an actual model
 */
class ConfirmBehavior extends ModelBehavior {

/**
 * Setup function
 *
 * @param object $Model The calling model
 */
	function setup(&$Model) {		
		$Model->RevisionModel = new Model(array(
			'table' => Inflector::tableize($Model->name).'_revs',
			'name' => 'Revision',
			'ds' => $Model->useDbConfig
		));
		$Model->RevisionModel->primaryKey = 'version_id';
	}

/**
 * Behavior::beforeSave callback
 *
 * Saves data into a different table instead of overwriting the original record
 * and awaits confirmation
 *
 * @param object $Model The calling model
 * @return boolean Success
 */
	function beforeSave(&$Model) {
		if (isset($Model->data[$Model->alias])) {
			$data = $Model->data[$Model->alias];
		} else {
			$data = $Model->data;
		}
		
		if (!$Model->id && isset($data['id'])) {
			$Model->id = $data['id'];
		}
		
		$original = $Model->read();
		$Model->data = $data;
		
		// compare fields to see if anything changed
		$changed = false;
		foreach ($original[$Model->alias] as $field => $value) {
			if (isset($data[$field]) && $data[$field] != $value) {
				$changed = true;
				break;
			}
		}
		
		if (!$changed) {
			return true;
		}

		// save to revision table
		$data['id'] = $Model->id;
		$data['version_created'] = date('Y-m-d H:i:s');

		// remove data so it doesn't save to the original model
		if (isset($Model->data[$Model->alias])) {
			$Model->data[$Model->alias] = array();
		} else {
			$Model->data = array();
		}

		return $Model->RevisionModel->save($data);
	}

/**
 * Gets the latest revision
 *
 * @param object $Model The calling model
 * @param integer $Model The id of the model to check for revisions of
 * @return array The revision
 */
	function revision(&$Model, $id = null) {
		if (!$id) {
			return false;
		}

		return $Model->RevisionModel->find('first', array(
			'conditions' => array(
				'Revision.id' => $id
			)
		));	
	}

/**
 * Confirms the latest revision
 *
 * @param object $Model The calling model
 * @param integer $Model The id of the model to check for revisions of
 * @return boolean Success
 */
	function confirmRevision(&$Model, $id = null) {
		// get revision changes
		$rev = $Model->RevisionModel->find('first', array(
			'conditions' => array(
				'Revision.id' => $id
			)
		));
		$rev_id = $rev['Revision']['version_id'];
		unset($rev['Revision']['version_id']);
		unset($rev['Revision']['version_created']);
		if (isset($rev['Revision']['id']) && (!$rev['Revision']['id'] || empty($rev['Revision']['id']))) {
			unset($rev['Revision']['id']);
		}
		$rev = Set::filter($rev['Revision']);

		// disable model
		$Model->Behaviors->disable('Confirm');
		// save revision
		$data = array(
			$Model->alias => $rev
		);
		$mSave = $Model->save($data);
		
		// remove revision
		$rDelete = $Model->RevisionModel->deleteAll(array(
			'Revision.id' => $id
		));
		
		// re-enable model
		$Model->Behaviors->enable('Confirm');

		return $mSave && $rDelete;
	}

/**
 * Denies the latest revision
 *
 * @param object $Model The calling model
 * @param integer $Model The id of the model to check for revisions of
 * @return boolean Success
 */
	function denyRevision(&$Model, $id = null) {	
		// remove revision
		return $Model->RevisionModel->deleteAll(array(
			'Revision.id' => $id
		));
	}

}
?>
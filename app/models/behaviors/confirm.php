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
 * @todo Refactor and wrap in a plugin, generally clean it up
 */
class ConfirmBehavior extends ModelBehavior {
	
/**
 * Setup function
 *
 * @param object $Model The calling model
 */
	function setup(&$Model) {
		$dbConfig = $Model->useDbConfig;
		
		$Model->RevisionModel = new Model(array(
			'table' => Inflector::tableize($Model->name).'_revs',
			'name' => 'Revision'
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
 */
	function beforeSave(&$Model) {
		$data = $Model->data[$Model->alias];

		// empty the data so it doesn't save the change
		$Model->data[$Model->alias] = array();

		// save to revision table
		$data['version_created'] = date('Y-m-d H:i');
		$Model->RevisionModel->save($data);
		
		return true;
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
				'id' => $id
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
			'id' => $id
		));	
		$rev_id = $rev['Revision']['version_id'];
		unset($rev['Revision']['version_id']);
		unset($rev['Revision']['version_created']);
		
		// disable model
		$Model->Behaviors->disable('Confirm');
		// save revision
		$data = array(
			$Model->alias => $rev['Revision']
		);		
		$mSave = $Model->save($data);
		
		// remove revision
		$rDelete = $Model->RevisionModel->deleteAll(array(
			'id' => $id
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
			'id' => $id
		));
	}

}
?>
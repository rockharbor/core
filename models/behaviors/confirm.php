<?php

class ConfirmBehavior extends ModelBehavior {
		
	function setup(&$Model) {
		$dbConfig = $Model->useDbConfig;
		
		$Model->RevisionModel = new Model(array(
			'table' => Inflector::tableize($Model->name).'_revs',
			'name' => 'Revision'
		));
		$Model->RevisionModel->primaryKey = 'version_id';
	}
	
	function beforeSave(&$Model) {	
		$data = $Model->data[$Model->alias];
		
		// empty the data so it doesn't save the change
		$Model->data[$Model->alias] = array();
		
		// save to revision table
		$data['version_created'] = date('Y-m-d H:i');
		$Model->RevisionModel->save($data);
		
		return true;
	}
	
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
	
	function confirmRevision(&$Model, $id = null) {
		// get revision changes
		$rev = $Model->RevisionModel->find('first', array(
			'id' => $id
		));	
		$rev_id = $rev['Revision']['version_id'];
		unset($rev['Revision']['version_id']);
		unset($rev['Revision']['version_created']);
		
		// detach model
		$Model->Behaviors->detach('Confirm');
		// save revision
		$data = array(
			$Model->alias => $rev['Revision']
		);		
		$mSave = $Model->save($data);
		
		// remove revision
		$rDelete = $Model->RevisionModel->deleteAll(array(
			'id' => $id
		));
		
		// reattach model
		$Model->Behaviors->attach('Confirm');
		
		return $mSave && $rDelete;
	}
	
	function denyRevision(&$Model, $id = null) {	
		// remove revision
		return $Model->RevisionModel->deleteAll(array(
			'id' => $id
		));
	}

}

?>
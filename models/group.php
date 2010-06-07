<?php
class Group extends AppModel {
	var $name = 'Group';
	
	var $hasMany = array('User');
	
	var $order = 'Group.parent_id ASC';
	
	var $actsAs = array(
		'Acl' => 'requester',
		'Tree'
	);		
	
	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		$data = $this->data;
		if (empty($this->data)) {
			$data = $this->read();
		}
		if (empty($data['Group']['parent_id'])) {
			return null;
		} else {
			return $data['Group']['parent_id'];
		}
	}
	
	/*function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		$data = $this->data;
		if (empty($this->data)) {
			$data = $this->read();
		}
		if (empty($data['User']['group_id'])) {
			return null;
		} else {
			return array('model' => 'Group', 'foreign_key' => $data['User']['group_id']);
		}
	}*/
}
?>
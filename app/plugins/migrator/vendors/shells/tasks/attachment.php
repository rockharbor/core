<?php

App::import('Lib', 'Media.MimeType');

class AttachmentTask extends MigratorTask {

	var $_documentTypeMap = array(
		'EVENT' => 'Involvement',
		'TEAM' => 'Involvement',
		'GROUP' => 'Involvement',
		'PERSON' => 'User'
	);

	var $_ownerTypeMap = array(
		'person' => 'User'
	);

	function migrate() {
		// import all
		$Document = new Model(false, 'documents', $this->_oldDbConfig);
		$Image = new Model(false, 'images', $this->_oldDbConfig);
		$oldDocuments = $Document->find('all', array(
			'conditions' => array(
				'created >=' => date('Y-m-d', strtotime('last month'))
			)
		));
		$this->out(date('Y-m-d', strtotime('last month')));
		$oldImages = $Image->find('all', array(
			'conditions' => array(
				'masterImage_id' => 0,
				'created >=' => date('Y-m-d', strtotime('last month'))
			)
		));

		$this->Mime = new MimeType();
		$this->Document = ClassRegistry::init('Document');
		$this->Image = ClassRegistry::init('Image');

		foreach ($oldDocuments as $oldDocument) {
			$oldDocument = $this->_prepareData($oldDocument);

			$friendly = explode('.', $oldDocument['displayname']);
			array_pop($friendly);
			$friendly = implode('.', $friendly);

			$newAttachment = array(
				'Document' => array(
					'model' => $oldDocument['document_type'],
					'foreign_key' => $oldDocument['type_id'],
					'alternative' => low($friendly),
					'group' => 'Document',
					'approved' => true,
					'created' => $oldDocument['created'],
					'file' => ROOT.DS.'attachments'.DS.$oldDocument['filename']
				)
			);

			$this->Document->model = $newAttachment['Document']['model'];
			$this->Document->create();
			$success = $this->Document->save($newAttachment);
			if (!$success) {
				$this->out('Couldn\'t save Document # '.$oldDocument['type_id']);
				$this->out(print_r($this->Document->validationErrors));
				$this->out($newAttachment['Document']['file']);
				break;
			}
		}

		foreach ($oldImages as $oldImage) {
			$oldImage = $this->_prepareData($oldImage);

			$newAttachment = array(
				'Image' => array(
					'model' => $oldImage['owner_type'],
					'foreign_key' => $oldImage['owner_id'],
					'alternative' => 'profile photo',
					'group' => 'Image',
					'approved' => true,
					'created' => $oldImage['created'],
					'file' => ROOT.DS.'attachments'.DS.$oldImage['filename'].'.'.$oldImage['extension']
				)
			);

			$this->Image->model = $newAttachment['Image']['model'];
			$this->Image->create();
			$success = $this->Image->save($newAttachment);
			if (!$success) {
				$this->out('Couldn\'t save Image # '.$oldImage['owner_id']);
				$this->out(print_r($this->Image->validationErrors));
				$this->out($newAttachment['Image']['file']);
				break;
			}
		}
	}
}
<?php
/* Attachment Fixture generated on: 2010-08-05 09:08:10 : 1281025930 */
class AttachmentFixture extends CakeTestFixture {
	var $name = 'Attachment';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'foreign_key' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'dirname' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'basename' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'checksum' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'alternative' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'group' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'approved' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model_key' => array('column' => array('model', 'foreign_key'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'model' => 'SysEmail',
			'foreign_key' => 'test',
			'dirname' => 'transfer/img',
			'basename' => 'image.jpg',
			'alternative' => 'Image',
			'group' => 'Image',
			'approved' => 1,
			'created' => '2010-03-29 13:35:39',
			'modified' => '2010-03-29 13:35:39'
		),
		array(
			'id' => 2,
			'model' => 'SysEmail',
			'foreign_key' => 'test',
			'dirname' => 'transfer/gen',
			'basename' => 'document.xlsx',
			'alternative' => 'List of people to remove from CORE',
			'group' => 'Document',
			'approved' => 1,
			'created' => '2010-03-26 13:35:16',
			'modified' => '2010-03-26 13:35:16'
		),
		array(
			'id' => 3,
			'model' => 'SysEmail',
			'foreign_key' => 'anotherTest',
			'dirname' => 'transfer/gen',
			'basename' => 'document2.xlsx',
			'alternative' => 'List of people to add to CORE',
			'group' => 'Document',
			'approved' => 1,
			'created' => '2010-03-26 13:35:16',
			'modified' => '2010-03-26 13:35:16'
		),
		array(
			'id' => 4,
			'model' => 'User',
			'foreign_key' => 1,
			'dirname' => 'img',
			'basename' => 'image.jpg',
			'alternative' => 'Profile photo',
			'group' => 'Image',
			'approved' => 0,
			'created' => '2010-03-26 13:35:16',
			'modified' => '2010-03-26 13:35:16'
		)
	);
}
?>
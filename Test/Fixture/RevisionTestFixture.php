<?php
/**
 * AnnouncementRevisionFixture
 *
 */
class RevisionTestFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'revision_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'unique'),
		'status_id' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3),
		'content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'create_user_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'revision_id' => array('column' => 'revision_id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'revision_id' => '10',
			'status_id' => '1',
			'content' => 'Test1',
			'created' => '2014-05-06 01:16:23',
			'modified' => '2014-05-06 01:18:31'
		),
	);

}

<?php
/**
 * RevisionBehavior Test Case
 *
 * @author   Ryuji Masukawa <masukawa@nii.ac.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('RevisionBehavior', 'Revision.Model/Behavior');

/**
 * Summary for RevisionBehavior Test Case
 */
class RevisionBehaviorTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.revision.revision_test',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Revision = new RevisionBehavior();
		$this->RevisionTest = ClassRegistry::init('Revision.RevisionTest');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Revision);
		unset($this->RevisionTest);

		parent::tearDown();
	}

/**
 * testBeforeSave method
 *
 * @return void
 */
	public function testBeforeSave() {
		$model = $this->__getModel();

		$result = $this->Revision->beforeSave($model);
		$this->assertFalse($result);

		$this->Revision->setup($model);
		$result = $this->Revision->beforeSave($model);
		$this->assertFalse($result);

		$settings = array(
			'modelName' => 'Revision.RevisionTest',
			'fields' => array('content'),
		);
		$this->Revision->setup($model, $settings);
		$result = $this->Revision->beforeSave($model);
		$this->assertFalse($result);
	}

/**
 * testBeforeSaveMock method
 *
 * @return void
 */
	public function testBeforeSaveMock() {
		$model = $this->__getModel();
		$settings = array(
			'modelName' => 'Revision.RevisionTest',
			'fields' => array('content'),
		);
		$model->data['RevisionTest'] = array(
			'id' => 1,
			'content' => '',
		);

		// Validate Error
		$mockRevisionTest = $this->getMock('RevisionTestMock', array('set', 'validates'));
		$mockRevisionTest->expects($this->once())
			->method('validates')
			->will($this->returnValue(false));
		$mock = $this->getMock('RevisionBehavior', array('_getRevisionModel'));
		$mock->expects($this->once())
			->method('_getRevisionModel')
			->will($this->returnValue($mockRevisionTest));

		$mock->setup($model, $settings);
		$result = $mock->beforeSave($model);
		$this->assertFalse($result);

		// Revision Model empty
		$mock2 = $this->getMock('RevisionBehavior', array('_getRevisionModel'));
		$mock2->expects($this->once())
			->method('_getRevisionModel')
			->will($this->returnValue(null));

		$mock2->setup($model, $settings);
		$result = $mock2->beforeSave($model);
		$this->assertFalse($result);
	}

/**
 * testAfterSave method
 *
 * @return void
 */
	public function testAfterSave() {
		$statusId = Configure::read('Revision.status_id');
		$model = $this->__getModel();

		$settings = array(
			'modelName' => 'Revision.RevisionTest',
			'fields' => array('content'),
		);
		$this->Revision->setup($model, $settings);

		$content = 'Update Content';
		$model->data['RevisionTest'] = array(
			'id' => 1,
			'content' => $content,
		);
		$result = $this->Revision->beforeSave($model);
		$this->assertTrue($result);
		$result = $this->Revision->afterSave($model, true);
		$this->assertTrue($result);
		$revisionTest = $this->RevisionTest->findById(1);
		$this->assertEquals($revisionTest['RevisionTest']['id'], 1);
		$this->assertEquals($revisionTest['RevisionTest']['status_id'], $statusId['published']);
		$this->assertEquals($revisionTest['RevisionTest']['content'], $content);

		$model->data['Revision'] = array();
		$result = $this->Revision->beforeSave($model);
		$this->assertTrue($result);
		$result = $this->Revision->afterSave($model, true);

		$this->assertTrue($result);
		$revisionTest = $this->RevisionTest->findById(1);
		$this->assertEquals($revisionTest['RevisionTest']['id'], 1);
		$this->assertEquals($revisionTest['RevisionTest']['status_id'], $statusId['draft']);
		$this->assertEquals($revisionTest['RevisionTest']['content'], $content);

		// save Error
		$this->Revision->revisionModel = $this->getMock('RevisionTest', array('save'));
		$this->Revision->revisionModel->expects($this->once())
			->method('save')
			->will($this->returnValue(false));
		$result = $this->Revision->afterSave($model, true);
		$this->assertFalse($result);
	}

/**
 * __getModel
 * @param  void
 * @return $model
 */
	private function __getModel() {
		$model = ClassRegistry::init('Model', 'Model');
		$model->alias = 'Revision';
		$model->id = 10;
		$model->data = array(
			'Revision' => array('is_published' => true)
		);
		return $model;
	}

}

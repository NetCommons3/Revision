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
 * testAfterSave method
 *
 * @return void
 */
	public function testAfterSave() {
		$statusId = Configure::read('Revision.status_id');
		$model = ClassRegistry::init('Model', 'Model');
		$model->alias = 'Revision';
		$model->id = 10;
		$model->data = array(
			'Revision' => array('is_published' => true)
		);
		$result = $this->Revision->afterSave($model, true);
		$this->assertFalse($result);

		$this->Revision->setup($model);
		$result = $this->Revision->afterSave($model, true);
		$this->assertFalse($result);

		$settings = array(
			'modelName' => 'Revision.RevisionTest',
		);
		$this->Revision->setup($model, $settings);
		$result = $this->Revision->afterSave($model, true);
		$this->assertFalse($result);

		$content = 'Update Content';
		$model->data['RevisionTest'] = array(
			'id' => 1,
			'content' => $content,
		);
		$result = $this->Revision->afterSave($model, true);
		$this->assertTrue($result);
		$revisionTest = $this->RevisionTest->findById(1);
		$this->assertEquals($revisionTest['RevisionTest']['id'], 1);
		$this->assertEquals($revisionTest['RevisionTest']['status_id'], $statusId['published']);
		$this->assertEquals($revisionTest['RevisionTest']['content'], $content);

		$model->data['Revision'] = array();
		$result = $this->Revision->afterSave($model, true);

		$this->assertTrue($result);
		$revisionTest = $this->RevisionTest->findById(1);
		$this->assertEquals($revisionTest['RevisionTest']['id'], 1);
		$this->assertEquals($revisionTest['RevisionTest']['status_id'], $statusId['draft']);
		$this->assertEquals($revisionTest['RevisionTest']['content'], $content);

		// save Error
		$this->Revision->revisionModel = $this->getMock('RevisionTest', array('save'));
		$result = $this->Revision->afterSave($model, true);
		$this->assertFalse($result);
	}
}

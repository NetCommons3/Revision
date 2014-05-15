<?php

App::uses('ModelBehavior', 'Model');

/**
 * Class RevisionBehavior
 *
 */
class RevisionBehavior extends ModelBehavior {

/**
 * Behavior settings
 *
 * @var array
 */
	public $settings = array();

/**
 * revisionModel
 *
 * @var false|object
 */
	public $revisionModel = false;

/**
 * Default setting values
 *
 * @var array
 */
	protected $_defaults = array();

/**
 * Setup the behavior and import required classes.
 *
 * @param \Model|object $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @return void
 */
	public function setup(Model $Model, $settings = null) {
		if (is_array($settings)) {
			$this->settings[$Model->alias] = array_merge($this->_defaults, $settings);
		} else {
			$this->settings[$Model->alias] = $this->_defaults;
		}
		$this->__setupSettings($Model);
	}

/**
 * afterSave, stop the timer started from a save.
 *
 * @param \Model $Model
 * @param string $created
 * @return boolean Always true
 */
	public function afterSave(Model $Model, $created, $options = array()) {
		if (!$this->__checkRevisionModel($Model)) {
			return false;
		}
		$modelName = $this->settings[$Model->alias]['modelName'];
		$this->__setRevisionData($Model);
		$Revision = $this->__getRevisionModel($modelName);
		if (empty($Revision) || !$Revision->save($Model->data) ) {
			return false;
		}
		return true;
	}

/**
 * __setupSettings
 *
 * @param \Model $Model
 * @return void
 */
	private function __setupSettings($Model) {
		if (isset($this->settings[$Model->alias]['modelName'])) {
			$modelName = $this->settings[$Model->alias]['modelName'];
			list($plugin, $className) = pluginSplit($modelName);
			$this->settings[$Model->alias]['plugin'] = $plugin;
			$this->settings[$Model->alias]['className'] = $className;
		}
		if (!isset($this->settings[$Model->alias]['foreignKey']) &&
			isset($this->settings[$Model->alias]['plugin'])) {
			$this->settings[$Model->alias]['foreignKey'] =
				Inflector::underscore(Inflector::singularize($this->settings[$Model->alias]['plugin'])) . '_id';
		}
	}

/**
 * __getRevisionStatusId
 *
 * @param \Model $Model
 * @return void
 */
	private function __setRevisionData($Model) {
		$className = $this->settings[$Model->alias]['className'];
		$Model->data[$className][$this->settings[$Model->alias]['foreignKey']] = $Model->id;
		if (array_key_exists('is_published', $Model->data[$Model->alias]) && $Model->data[$Model->alias]['is_published']) {
			$Model->data[$className]['status_id'] = REVISION_STATUS_PUBLISHED;
		} else {
			$Model->data[$className]['status_id'] = REVISION_STATUS_DRAFT;
		}
	}

/**
 * __checkRevisionModel
 *
 * @param \Model $Model
 * @return boolean
 */
	private function __checkRevisionModel($Model) {
		if (!isset($this->settings[$Model->alias]['modelName']) ||
			!isset($this->settings[$Model->alias]['className']) ||
			!isset($this->settings[$Model->alias]['foreignKey'])) {
			return false;
		}
		$className = $this->settings[$Model->alias]['className'];
		if (!isset($Model->data[$className])) {
			return false;
		}
		return true;
	}

/**
 * __getRevisionModel
 *
 * @param string $modelName
 * @return \Model $Model
 */
	private function __getRevisionModel($modelName) {
		if (empty($this->revisionModel)) {
			$this->revisionModel = ClassRegistry::init($modelName);
		}
		return $this->revisionModel;
	}
}

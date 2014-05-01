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
 * Before save method. Called before all saves
 * @param Model $Model Model instance
 * @param array $options Options passed from Model::save().
 * @return boolean true to continue, false to abort the save
 */
	public function beforeSave(Model $Model, $options = array()) {
		return true;
	}

/**
 * afterSave, stop the timer started from a save.
 *
 * @param \Model $Model
 * @param string $created
 * @return boolean Always true
 */
	public function afterSave(Model $Model, $created, $options = array()) {
		if (!isset($this->settings[$Model->alias]['modelName']) ||
			!isset($this->settings[$Model->alias]['className']) ||
			!isset($this->settings[$Model->alias]['foreignKey'])) {
			return false;
		}
		$modelName = $this->settings[$Model->alias]['modelName'];
		$className = $this->settings[$Model->alias]['className'];
		if (isset($Model->data[$className])) {
			$Model->data[$className][$this->settings[$Model->alias]['foreignKey']] = $Model->id;
			if (array_key_exists('is_published', $Model->data[$Model->alias]) && $Model->data[$Model->alias]['is_published']) {
				$Model->data[$className]['status_id'] = REVISION_STATUS_PUBLISHED;
			} else {
				$Model->data[$className]['status_id'] = REVISION_STATUS_DRAFT;
			}
			$Revision = ClassRegistry::init($modelName);
			if (!$Revision->save($Model->data) ) {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}
}

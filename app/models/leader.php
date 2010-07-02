<?php
/**
 * Leader model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Leader model
 *
 * Polymorphic model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Leader extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Leader';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'User',
		'Campus' => array(
			'foreignKey' => 'model_id',
			'conditions' => array(
				'Leader.model' => 'Campus'
			)
		),
		'Ministry' => array(
			'foreignKey' => 'model_id',
			'conditions' => array(
				'Leader.model' => 'Ministry'
			)
		),
		'Involvement' => array(
			'foreignKey' => 'model_id',
			'conditions' => array(
				'Leader.model' => 'Involvement'
			)
		)
	);

/**
 * Gets the managers for this model's record.
 *
 * Managers are the Leaders for the model's parent model. Only Involvements and
 * Ministries have managers.
 *
 * @param string $model The model
 * @param integer $modelId The id of the model to pull managers for
 * @return array
 * @access public
 */ 
	function getManagers($model = null, $modelId = null) {
		if (!$model || !$modelId || !($model == 'Involvement' || $model == 'Ministry')) {
			return false;
		}
		
		// get managers based on type
		$this->bindModel(array(
			'belongsTo' => array(
				$model => array(
					'foreignKey' => 'model_id'
				)
			)
		));

		$parentModel = $model == 'Involvement' ? 'Ministry' : 'Campus';
		
		$this->{$model}->contain(false);
		$item = $this->{$model}->read(
			array(
				$model.'.id',
				strtolower($parentModel.'_id')
			),
			$modelId
		);

		if (empty($item)) {
			return false;
		}

		$parentModelId = $item[$model][strtolower($parentModel.'_id')];

		$managers = $this->find('all', array(
			'conditions' => array(
				'model' => $parentModel,
				'model_id' => $parentModelId
			),
			'contain' => array(
				'User' => array(
					'Profile'
				)
			)
		));
			
		return $managers;
	}
}
?>
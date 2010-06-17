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
		'User'
	);

/**
 * Gets the managers for this model's record. Only Involvements and Ministries have managers. 
 *
 * @param string $model The model
 * @param integer $modelId The id of the model to pull managers for
 * @return array
 * @access public
 */ 
	function getManagers($model = null, $modelId = null) {
		if (!$model || !$modelId || $model == 'Campus') {
			return array();
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
		
		$item = $this->find('first', array(
			'conditions' => array(
				'model' => $model,
				'model_id' => $modelId
			),
			'contain' => array(
				$model => array(
					$parentModel
				)
			)
		));			
			
		$parentModelId = $item[$model][$parentModel]['id'];
		
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
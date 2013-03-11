<?php
/**
 * Address model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Address model
 *
 * Polymorphic model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Address extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'Address';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'GeoCoordinate'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
		'Zipcode' => array(
			'foreignKey' => false,
			'conditions' => array('Zipcode.zip = Address.zip')
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'rule' => array('custom', '/^[a-z0-9 ]*$/i'),
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please use alpha and numeric characters only.'
		),
		'zip' => array(
			'rule' => array('postal', null, 'us'),
			'message' => 'Please enter a valid zipcode.',
			'allowEmpty' => false
		)
	);

/**
 * `Model::beforeSave` callback to ensure a name is added, so when `$displayField`
 * is used in calls such as `Model::find('list');` it is populated with at least
 * something for the user to choose.
 *
 * @return true Continue with the save
 */
	public function beforeSave() {
		if (empty($this->data[$this->alias]['name'])) {
			if (!empty($this->data[$this->alias]['city'])) {
				$this->data[$this->alias]['name'] = $this->data[$this->alias]['city'].' Address';
			} elseif (!empty($this->data[$this->alias]['zip'])) {
				$this->data[$this->alias]['name'] = $this->data[$this->alias]['zip'].' Address';
			}
		}
		return true;
	}

/**
 * Returns a Cake virtual field SQL string for the distance
 * from a latitude and longitude
 *
 * @param float $lat Latitude
 * @param float $lng Longitude
 * @return string SQL string
 */
	public function distance($lat = null, $lng = null) {
		if (!$lat || !$lng) {
			return null;
		}
		return "(
			(ACOS(SIN($lat * PI() / 180)
			* SIN(Address.lat * PI() / 180)
			+ COS($lat * PI() / 180)
			* COS(Address.lat * PI() / 180)
			* COS(($lng - Address.lng) * PI() / 180))
			* 180 / PI()) * 60 * 1.1515
		)";
	}

/**
 * Gets a list of Address ids that have the same model and foreign key as this
 * address
 *
 * @param integer $id The id of the Address
 * @return array List of ids
 */
	public function related($id = null) {
		$address = $this->read(null, $id);
		if (!$address) {
			return false;
		}
		$addresses = $this->find('list', array(
			'conditions' => array(
				'Address.foreign_key' => $address['Address']['foreign_key'],
				'Address.model' => $address['Address']['model'],
				'Address.id <>' => $id
			)
		));
		return array_keys($addresses);
	}

/**
 * Toggles Address active field. Only toggles if the address exists and you are
 * not trying to deactivate the primary address.
 *
 * @param integer $id The address id
 * @param boolean $active Whether to activate or deactivate
 * @return boolean Success
 */
	public function toggleActivity($id = null, $active = false) {
		$address = $this->read(null, $id);
		if ($address && !(!$active && $address['Address']['primary']) || $address['Address']['model'] != 'User') {
			return parent::toggleActivity($id, $active, false);
		}
		return false;
	}

/**
 * Sets the primary address
 *
 * @param integer $id
 */
	public function setPrimary($id) {
		$this->id = $id;
		$primaryAddress = $this->read();
		$otherAddresses = $this->find('all', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'Address.foreign_key' => $primaryAddress[$this->alias]['foreign_key'],
				'Address.model' => $primaryAddress[$this->alias]['model'],
				'Address.id <>' => $id
			)
		));
		foreach ($otherAddresses as $otherAddress) {
			$this->id = $otherAddress['Address']['id'];
			$this->saveField('primary', false);
		}

		$this->id = $id;
		$this->saveField('primary', true);
	}
}

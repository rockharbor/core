<?php
/**
 * Sluggable route class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       slugger
 * @subpackage    slugger.libs.routes
 */

/**
 * Sluggable Route
 *
 * Automatically slugs routes based on named parameters
 *
 * @package       slugger
 * @subpackage    slugger.libs.routes
 * @todo benchmark the caching, maybe change it to use a faster cache if slow
 * @link http://mark-story.com/posts/view/using-custom-route-classes-in-cakephp
 */
class SluggableRoute extends CakeRoute {

/*
 * Override the parsing function to find an id based on a slug
 *
 * @param string $url Url string
 * @return boolean
 */
    function parse($url) {
		$params = parent::parse($url);

		if (empty($params)) {
			return false;
		}

		if (isset($this->options['models']) && isset($params['_args_'])) {
			foreach ($this->options['models'] as $checkNamed => $slugField) {
				if (is_numeric($checkNamed)) {
					$checkNamed = $slugField;
					$slugField = null;
				}
				$Model = ClassRegistry::init($checkNamed);
				if ($Model === false) {
					continue;
				}
				$slugSet = $this->getSlugs($Model, $slugField);				
				$slugSet = array_flip($slugSet);
				$passed = explode('/', $params['_args_']);
				foreach ($passed as $key => $pass) {
					if (isset($slugSet[$pass])) {
						unset($passed[$key]);
						$passed[] = $Model->name.':'.$slugSet[$pass];
					}
				}
				$params['_args_'] = implode('/', $passed);
			}
			return $params;
		}
		
		return false;
	}

/*
 * Matches the model's id and converts it to a slug
 *
 * @param array $url Cake url array
 * @return boolean
 */
	function match($url) {
		// grab id and convert to username (from the user param)
		if (isset($this->options['models'])) {
			foreach ($this->options['models'] as $checkNamed => $slugField) {
				if (is_numeric($checkNamed)) {
					$checkNamed = $slugField;
					$slugField = null;
				}
				if (isset($url[$checkNamed])) {
					$Model = ClassRegistry::init($checkNamed);
					if ($Model === false) {
						continue;
					}
					$slugSet = $this->getSlugs($Model, $slugField);
					
					if (isset($slugSet[$url[$checkNamed]])) {
						$url[] = $slugSet[$url[$checkNamed]];
						unset($url[$checkNamed]);
					}
				}
			}
		}
		
		return parent::match($url);
	}

/**
 * Slugs a string for the purpose of this route
 *
 * @param integer $id The key for the set
 * @param array $set The set
 * @return string
 */
	function slug($id, $set) {
		$str = $set[$id];
		$counts = array_count_values($set);
		if ($counts[$str] > 1 || (isset($this->options['prependPk']) && $this->options['prependPk'])) {
			$str = $id.' '.$str;
		}
		return $this->_slug($str);
	}

/**
 * Slugs a string
 *
 * @param string $str The string to slug
 * @return string
 */
	function _slug($str) {
		return strtolower(Inflector::slug($str, '-'));
	}

/**
 * Gets slugs from cache
 *
 * @param Model $Model
 */
	function getSlugs(&$Model, $field = null) {
		$cache = Cache::getInstance();
		$originalCacheConfig = $cache->__name;
		Cache::config('Slugger.short', array(
			'duration' => '+1 days'
		));

		if (!$field) {
			$field = $Model->displayField;
		}

		$results = Cache::read('slugger_'.$Model->name.'_slugs', 'Slugger.short');
		if (empty($results)) {
			$results = $Model->find('list', array(
				'fields' => array(
					$Model->name.'.'.$Model->primaryKey,
					$Model->name.'.'.$field,
				),
				'recursive' => -1
			));
			Cache::write('slugger_'.$Model->name.'_slugs', $results, 'Slugger.short');
		}
		$results = Set::filter($results);
		$slugs = array_map(array($this, '_slug'), $results);
		foreach ($slugs as $key => &$slug) {
			$slug = $this->slug($key, $results);
		}
		
		Cache::config($originalCacheConfig);
		return $slugs;
	}
}

?>
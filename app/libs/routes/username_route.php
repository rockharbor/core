<?php
/**
 * Username route class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.libs.routes
 */

/**
 * Username Route
 *
 * Basically rewrites url's to use the username in place of the user's id. A url
 * like /users/edit/User:1 becomes /users/edit/jharris
 *
 * @package       core
 * @subpackage    core.app.libs.routes
 * @todo Activate and benchmark with ALL records
 */
class UsernameRoute extends CakeRoute {

/*
 * Override the parsing function to convert a username to an id
 * for the controller (thanks to mark story's blog about custom routing)
 *
 * @param string $url See CakeRoute
 * @return boolean
 */
    function parse($url) {
		$params = parent::parse($url);

		if (empty($params)) {
			return false;
		}
		
		// check cache		
		$usernames = Cache::read('username_routes');
		if (empty($usernames)) {
			App::import('Model', 'User');
			$User = new User();
			$users = $User->find('all', array(
				'fields' => array('User.id', 'User.username'),
				'recursive' => -1
			));
			
			$usernames = Set::combine($users, '{n}.User.username', '{n}.User.id');
			Cache::write('username_routes', $usernames);
		}
		
		// set the user named param to the id (like it was originally passed)		
		if (isset($usernames[$params['user']])) {
			$params['named']['user'] = $usernames[$params['user']];
			
			return $params;
		}
		
		return false;
	}

/*
 * Override matching function to convert the id back to a username
 *
 * @param array $url Cake url array
 * @return boolean
 */
	function match($url) {
		// grab id and convert to username (from the user param)
		if (isset($url['user'])) {
			App::import('Model', 'User');
			$User = new User();
			$users = $User->find('all', array(
				'fields' => array('User.id', 'User.username'),
				'recursive' => -1
			));	
			
			$usernames = Set::combine($users, '{n}.User.id', '{n}.User.username');
			$url['user'] = $usernames[$url['user']];
		}
		
		return parent::match($url);
	} 
}

?>
<?php
/**
 * Routes file
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.config
 */

/**
  * Extensions to redirect views/layouts
  */
	Router::parseExtensions('json', 'csv', 'print');

/**
 * Bring in custom routing libraries
 */
	App::uses('SluggableRoute', 'Slugger.Lib');

/**
 * Static routes
 */
	Router::connect('/', array('controller' => 'profiles', 'action' => 'view'));
	Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
	Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
	Router::connect('/pages/phrase/*', array('controller' => 'pages', 'action' => 'phrase'));
	Router::connect('/pages/message/*', array('controller' => 'pages', 'action' => 'message'));

/**
 * Custom routes
 */
	Router::connectNamed(array('User', 'Ministry', 'Involvement', 'Campus', 'model'), array('defaults' => true));
	Router::connect('/involvements/:action/*', array('controller' => 'involvements'), array(
		'routeClass' => 'SluggableRoute',
		'models' => array('Involvement'),
		'prependPk' => true
	));
	Router::connect('/ministries/:action/*', array('controller' => 'ministries'), array(
		'routeClass' => 'SluggableRoute',
		'models' => array('Ministry'),
		'prependPk' => true
	));
	Router::connect('/campuses/:action/*', array('controller' => 'campuses'), array(
		'routeClass' => 'SluggableRoute',
		'models' => array('Campus'),
		'prependPk' => true
	));
	Router::connect('/:controller/:action/*', array(), array(
		'routeClass' => 'SluggableRoute',
		'models' => array('User'),
		'controller' => 'users|profiles',
		'prependPk' => true
	));

/*
 * Asset Compress
 */
	Router::connect('/css_cache/*', array('plugin' => 'asset_compress', 'controller' => 'css_files', 'action' => 'get'));
	Router::connect('/js_cache/*', array('plugin' => 'asset_compress', 'controller' => 'js_files', 'action' => 'get'));

/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes.  See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
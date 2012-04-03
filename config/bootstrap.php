<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

/**
 * As of 1.3, additional rules for the inflector are added below
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Bring in Core configuration class
 */
require_once APP.'libs'.DS.'core.php';

/**
 * Bring in and overwrite (specific) Media plugin settings
 */
require_once APP.'plugins'.DS.'media'.DS.'config'.DS.'core.php';

/**
 * Custom filters based on model
 */
Configure::write('Core.mediafilters.user', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(90, 90)),
));
Configure::write('Core.mediafilters.involvement', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(260, 90)),
));
Configure::write('Core.mediafilters.ministry', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(260, 90)),
));
// fallback defaults
Configure::write('Core.mediafilters.default', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(90, 90)),
));

/**
 * Referee plugin constant to define path
 */
define('LISTENER_PATH', APP . 'libs' . DS . 'listeners');

/**
 * Set QueueEmail to save the emails in the db 
 */
Configure::write('QueueEmail.deleteAfter', false);

/**
 * Load CORE plugin bootstraps
 */
$plugins = Core::read('plugin');
if (!empty($plugins)) {
	$plugins = array_keys($plugins);
	foreach ($plugins as $plugin) {
		App::import('Plugin', Inflector::camelize($plugin). 'Bootstrap');
	}
}

/**
 * Converts `<br />`'s to newlines
 * 
 * @param string $input
 * @return string
 */
function br2nl($input) {
	return preg_replace('/<br(\s+)?\/?>/i', PHP_EOL, $input);
}
?>
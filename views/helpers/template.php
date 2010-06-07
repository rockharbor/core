<?php

/**
 * Aids in creating fancible text from templates
 *
 * @author 		Jeremy Harris <jharris@rockharbor.org>
 * @package		app
 * @subpackage	app.views.helpers
 */
class TemplateHelper extends AppHelper {

/**
 * Creates a notification from a record and template
 *
 * @param array $notification The notification record
 * @param string $template The template
 * @return string
 * @see NotificationTemplatesController::add()
 */
	function notification($notification = array(), $template = '') {
		// extraction function
		global $notificationData;
		$notificationData = $notification;
		
		$extract = create_function(
			'$str',
			'global $notificationData; 
			return Set::classicExtract(array($notificationData), \'0.\'.$str[1]);'
		); 
		
		// search and replace!
		$txt = preg_replace_callback(
			'/%(.*?)%/', 
			$extract, 
			$template
		);
		
		unset($notificationData);
		
		return $txt;
	}
}

?>
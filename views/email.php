<?php
/**
 * Email view class.
 *
 * @copyright     Copyright 2013, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.views
 */

/**
 * Includes
 */
App::import('View', 'View');
App::import('Vendor', 'CssToInlineStyles', array(
	'file' => 'CssToInlineStyles' . DS . 'css_to_inline_styles.php'
));

/**
 * Email View
 *
 * All emails sent through core are rendered by this view class.
 *
 * @package       core
 * @subpackage    core.app.views
 */
class EmailView extends View {

/**
 * Renders the layout and includes the css stylesheet for inlining the styles
 *
 * @param string $content_for_layout
 * @param string $layout
 * @return string
 */
	public function renderLayout($content_for_layout, $layout = null) {
		$output = parent::renderLayout($content_for_layout, $layout);
		$css = file_get_contents(CSS . 'email.css');
		$inliner = new CssToInlineStyles($output, $css);
		return $inliner->convert();
	}

}

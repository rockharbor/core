<?php
/**
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
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Core::read('general.site_name_tagless').' '.Core::read('version').' :: '.$title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// CORE css
		echo $this->Html->css('reset');
		echo $this->Html->css('960');
		echo $this->Html->css('font-face');
		echo $this->Html->css('jquery-ui');
		echo $this->Html->css('styles');
		echo $this->Html->css('public');
		if(preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
			echo $this->Html->css('ie');
		}

		// google cdn scripts
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.js');

		// vendor scripts
		echo $this->Html->script('jquery.plugins/jquery.qtip');
		echo $this->Html->script('jquery.plugins/jquery.equalheights');

		// CORE scripts
		echo $this->Html->script('functions');
		echo $this->Html->script('global');
		echo $this->Html->script('ui');
		echo $this->Html->script('form');

		// setup
		$this->Js->buffer('CORE.initFormUI()');
		echo $this->Js->writeBuffer();
		echo $scripts_for_layout;

	?>
</head>
<body>
	<div class="container_12" id="wrapper">
		<div id="content-container" class="container_12 clearfix">
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->Session->flash(); ?>
			<?php echo $content_for_layout; ?>
		</div>
	</div>
</body>
</html>
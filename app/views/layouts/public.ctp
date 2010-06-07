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
		<?php echo $CORE['settings']['site_name_tagless'].' '.$CORE['version'].' :: '.$title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/themes/flick/jquery-ui.css');
		
		// google cdn scripts
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.js');
		
		// vendor scripts
		echo $this->Html->script('jquery.plugins/jquery.scrollTo');
		echo $this->Html->script('jquery.plugins/jquery.cookie');
		
		// CORE scripts
		echo $this->Html->script('global');		
		echo $this->Html->script('ui');
		echo $this->Html->script('form');
		
		$this->Js->buffer('CORE.attachModalBehavior();');
		$this->Js->buffer('$(\'div[id^=flash]\').delay(5000).slideUp();');
		
		echo $scripts_for_layout;
		
		// extra js
		echo $this->Js->writeBuffer();

	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php 
			
			echo $this->Html->link($CORE['settings']['site_name'].' '.$CORE['version'], '/', array('escape' => false)); 
			echo ' | ';			
			echo $this->Html->link('Login', array('controller' => 'users', 'action' => 'login'));
			echo ' | ';
			echo $this->Html->link('Register', array('controller' => 'users', 'action' => 'add'));
			
			
			?></h1>
		</div>
		<div id="content">
			
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->Session->flash(); ?>
			
			<?php echo $content_for_layout; ?>

		</div>
	</div>
	<div id="modal"></div>	
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
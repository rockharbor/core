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

		// CORE css
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/flick/jquery-ui.css');
		
		// vendor css
		echo $this->Html->css('jquery.wysiwyg');
		
		// google cdn scripts
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.js');
		echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=false');
		
		// vendor scripts
		echo $this->Html->script('jquery.plugins/jquery.scrollTo');
		echo $this->Html->script('jquery.plugins/jquery.cookie');
		echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
		
		// CORE scripts
		echo $this->Html->script('global');
		echo $this->Html->script('ui');
		echo $this->Html->script('form');
		
		// setup
		$this->Js->buffer('CORE.showValidationErrors();');
		$this->Js->buffer('CORE.attachModalBehavior();');
		$this->Js->buffer('$(\'div[id^=flash]\').delay(5000).slideUp();');
		$this->Js->buffer('CORE.autoComplete("SearchQuery", "'.Router::url(array(
			'controller' => 'searches',
			'action' => 'index',
			'ext' => 'json'
		)).'", function(item) {
			redirect(item.action);
		})');
		
		echo $scripts_for_layout;
		
		// extra js
		echo $this->Js->writeBuffer();

	?>
</head>
<body>
	<div id="container">
		<?php
		if (!empty($activeUser['Alert'])) {
		?>
		<div class="alerts">
			<div class="alert <?php echo $activeUser['Alert']['importance']; ?>">
				<h1><?php echo $activeUser['Alert']['name'];?></h1>
				<p><?php echo $this->Text->truncate($activeUser['Alert']['description'], 20);?></p>
				<p><?php echo $this->Html->link('Read this alert', array('controller' => 'alerts', 'action' => 'view', $activeUser['Alert']['id']), array('rel' => 'modal-none')); ?></p>
			</div>
		</div>
		<?php
		}
		?>
		<div id="header">
			<h1><?php			
			echo $this->Html->link($CORE['settings']['site_name'].' '.$CORE['version'], '/', array('escape' => false)); 		
			echo ' | ';
			echo 'What\'s up, '.$activeUser['Profile']['name'].'?';	
			echo ' | ';			
			echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'));
			echo ' | ';
			echo $this->Html->link('Profile', array('controller' => 'users', 'action' => 'edit_profile', 'User'=>$activeUser['User']['id']));
			echo ' | ';
			echo $this->Html->link('Notifications ('.$activeUser['User']['new_notifications'].' new)', array('controller' => 'notifications', 'action' => 'index'));
			echo ' | ';
			echo $this->Html->link('Alerts ('.$activeUser['User']['new_alerts'].' new)', array('controller' => 'alerts', 'action' => 'history'));
			echo '<br />Other things to do: ';
			echo $this->Html->link('View Ministries', array('controller' => 'ministries', 'action' => 'index'));
			echo ', ';
			echo $this->Html->link('Search People', array('controller' => 'searches', 'action' => 'user'));
			echo ', ';
			echo $this->Html->link('Run Reports', array('controller' => 'reports', 'action' => 'index'));
			echo ', ';
			echo $this->Html->link('View API', array('controller' => 'api_classes', 'plugin' => 'api_generator'));
			?>
			</h1>
		</div>
		<div id="debug">
		<strong>Debugging tools</strong>&nbsp;&nbsp;
			<?php
			echo $this->Html->image('icons/bug.png').$this->Html->link(' Report a bug on this page', array('controller' => 'sys_emails', 'action' => 'bug_compose'), array('rel' => 'modal-none'));
			echo '&nbsp;&nbsp;';
			echo $this->Html->image('icons/report.png').$this->Html->link(' View activity logs', array('controller' => 'logs', 'action' => 'index'), array('rel' => 'modal-none'));
			echo '&nbsp;&nbsp;';
			echo $this->Html->image('icons/database.png').$this->Html->link(' View error logs', array('controller' => 'errors', 'action' => 'index'), array('rel' => 'modal-none'));
			?>
		</div>
		<div id="search">
		<?php
			echo $this->Form->create('Search', array(
				'url' => array(
					'controller' => 'searches',
					'action' => 'index'
				),
				'inputDefaults' => array(
					'div' => false
				)
			));
			echo $this->Form->input('Search.query', array(
				'label' => 'Search (with super find-as-you-type action!):'
			));
			echo $this->Form->end('Go');
		?>
		</div>
		<div id="modal"></div>
		<div id="notification" style="display:none;"><div id="notification_content"><?php echo $this->Html->image('indicator.gif', array('border:none')); ?>&nbsp;Please wait...</div></div>
		<div id="content">
		
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->Session->flash(); ?>
			
			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt'=> __('CakePHP: the rapid development php framework', true), 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>	
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
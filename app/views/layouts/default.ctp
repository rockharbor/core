<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Core::read('site_name_tagless').' '.Core::read('version').' :: '.$title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// vendor css
		echo $this->Html->css('jquery.wysiwyg');

		// CORE css
		echo $this->Html->css('960');
		echo $this->Html->css('font-face');
		echo $this->Html->css('menu');		
		echo $this->Html->css('jquery-ui');
		echo $this->Html->css('styles');
		if(preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
			echo $this->Html->css('ie');
		}

		// google cdn scripts
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.js');
		echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=false');
		
		// vendor scripts
		echo $this->Html->script('jquery.plugins/jquery.cookie');
		echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
		
		// CORE scripts
		echo $this->Html->script('global');
		echo $this->Html->script('ui');
		echo $this->Html->script('form');
		
		// setup
		$this->Js->buffer('CORE.init()');		
		
		echo $scripts_for_layout;
	?>
</head>
<body>
	<div class="container_12" id="wrapper">
		<div class="container_12 clearfix" id="header">
			<div class="grid_10 main-nav-menu" id="primary">
				<ul>
					<li><?php echo $this->Html->link('☻', '/'); ?></li>
					<li id="nav-profile"><?php echo $this->Html->link('Profile', array('controller' => 'users', 'action' => 'edit_profile', 'User' => $activeUser['User']['id'])); ?>
						<ul>
							<li>
								<?php
										if (count($activeUser['Image']) > 0) {
											echo '<div class="profile-image">';
											$path = 's'.DS.$activeUser['Image'][0]['dirname'].DS.$activeUser['Image'][0]['basename'];
											echo $this->Media->embed($path, array('restrict' => 'image'));
											echo '</div>';
										}
								?>
								<div class="profile-information">
									<?php
										echo '<div class="profile-name">'.$activeUser['Profile']['name'].'</div>';
										echo '<div class="profile-address">';
										echo $activeUser['ActiveAddress']['address_line_1'];
										if (!empty($activeUser['ActiveAddress']['address_line_2'])) {
											echo '<br />'.$activeUser['ActiveAddress']['address_line_2'];
										}
										echo '<br />'.$activeUser['ActiveAddress']['city'].', '.$activeUser['ActiveAddress']['state'].' '.$activeUser['ActiveAddress']['zip'];
										echo '<div>'.$this->Html->link('Change', array('controller' => 'users', 'action' => 'edit_profile', 'User' => $activeUser['User']['id'], '#' => 'addresses'));
										echo '</div>';
										echo '</div>';
									?>
								</div>
								<div style="clear:both" />
							</li>
							<li class="profile-link"><?php echo $this->Html->link('My Involvement', array('controller' => 'rosters', 'action' => 'involvement', 'User' => $activeUser['User']['id'])); ?></li>
							<li class="profile-link"><?php echo $this->Html->link('My Household', array('controller' => 'households', 'User' => $activeUser['User']['id'])); ?></li>
							<li class="profile-link"><?php echo $this->Html->link('My Payments', array('controller' => 'payments', 'User' => $activeUser['User']['id'])); ?></li>
						</ul>
					</li>
					<li id="nav-notifications"><?php
					$new = count(Set::extract('/Notifications[read=0]', $activeUser['Notification']));
					$new += count($activeUser['Alert']);
					echo $this->Html->link('Notifications ('.$new.')', array('controller' => 'notifications', 'action' => 'index'));
					?>
						<ul>
							<?php
								foreach ($activeUser['Alert'] as $alert) {
									echo '<li>';
									$name = $this->Html->tag('div', $alert['Alert']['name'], array('class' => 'alert-name'));
									$desc = $this->Html->tag('div', $this->Text->truncate($alert['Alert']['description'], 100), array('class' => 'alert-description'));
									echo $this->Html->link($name.$desc, array('controller' => 'alerts', 'action' => 'view', $alert['Alert']['id']), array('escape' => false));
									echo '</li>';
								}

								foreach ($activeUser['Notification'] as $notification) {
									$class = $notification['Notification']['read'] ? 'read' : 'unread';
									echo '<li id="notification-'.$notification['Notification']['id'].'" class="'.$class.'"><p>';
									echo $this->Text->truncate($notification['Notification']['body'], 100, array('html' => true));
									echo $this->Js->link('[X]', array(
										'controller' => 'notifications',
										'action' => 'delete',
										$notification['Notification']['id']
									), array(
										'complete' => '$("#notification-'.$notification['Notification']['id'].'").remove()',
										'class' => 'delete'
									));
									echo '</p></li>';
									if ($class == 'unread') {
										$this->Js->buffer('$("#notification-'.$notification['Notification']['id'].'").bind("mouseenter", function() {
											CORE.request("'.Router::url(array('controller' => 'notifications', 'action' => 'read', $notification['Notification']['id'])).'");
											$(this).unbind("mouseenter");
											$(this).animate({borderLeftColor:"#fff"}, "slow");
										});');
									}
								}
							?>
						</ul>
					</li>
					<li id="nav-ministries">
						<?php echo $this->Html->link('Ministries', array('controller' => 'ministries')); ?>
						<ul>
							<?php
							foreach ($ministries as $ministry) {
								echo '<li>';
								echo $this->Html->link($ministry['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id']), array('class' => 'parent'));
								$childrenLinks = array();
								foreach ($ministry['ChildMinistry'] as $childMinistry) {
									$childrenLinks[] = $this->Html->link($childMinistry['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $childMinistry['id']), array('class' => 'child'));
								}
								if (count($childrenLinks) > 0) {
									echo implode(', ', $childrenLinks);
								}
								echo '</li>';
							}
							?>
						</ul>
					</li>
					<li><?php echo $this->Html->link('Calendar', array('controller' => 'dates', 'action' => 'calendar')); ?></li>
					<?php if (Configure::read()): ?>
					<li><?php echo $this->Html->link('Debugging', array('controller' => 'reports', 'action' => 'index')); ?>
						<ul><li><?php
					echo $this->Html->link('Report a bug on this page', array('controller' => 'sys_emails', 'action' => 'bug_compose'), array('rel' => 'modal-none'));
					echo $this->Html->link('View activity logs', array('controller' => 'logs', 'action' => 'index'), array('rel' => 'modal-none'));
					?></li></ul>
					</li>
					<?php endif; ?>
				</ul>
				<div id="nav-search">
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
							'label' => false,
							'value' => 'Search CORE',
							'size' => 30,
							'class' => 'search-out'
						));
						echo $this->Form->button(
							$this->Html->tag('span', '&nbsp;', array('class' => 'ui-button-icon-primary ui-icon ui-icon-search')),
							array(
								'escape' => false
							)
						);
						echo $this->Form->end();
					?></div>
			</div>
			<div class="grid_2" id="secondary">
				<?php
				echo $this->Html->link('View API', array('controller' => 'api_classes', 'plugin' => 'api_generator'));
				echo ' / ';
				echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'));
				?>
			</div>
		</div>
		<div id="content-container" class="container_12 clearfix">
			<div id="content" class="grid_10 prefix_1 suffix_1">
				<?php echo $this->Session->flash('auth'); ?>
				<?php echo $this->Session->flash(); ?>

				<?php echo $content_for_layout; ?>
			</div>
		</div>
		<div id="footer" class="container_12 clearfix">
		</div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
</body>
</html>
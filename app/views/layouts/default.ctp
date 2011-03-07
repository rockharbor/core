<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Core::read('general.site_name_tagless').' '.Core::read('version').' :: '.$title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// vendor css
		$this->AssetCompress->css('jquery.wysiwyg');
		$this->AssetCompress->css('fullcalendar');

		// CORE css
		$this->AssetCompress->css('reset');
		$this->AssetCompress->css('960');
		$this->AssetCompress->css('960-modal');
		$this->AssetCompress->css('font-face');
		$this->AssetCompress->css('menu');
		$this->AssetCompress->css('jquery-ui');
		$this->AssetCompress->css('styles');
		$this->AssetCompress->css('tables');

		$browser = get_browser($_SERVER['HTTP_USER_AGENT']);
		if($browser->browser == 'IE' && $browser->majorver < 9) {
			$this->AssetCompress->css('ie');
		}		
		$this->AssetCompress->css('calendar');

		// google cdn scripts
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.js');
		echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=false');
		
		// vendor scripts
		$this->AssetCompress->script('jquery.plugins/jquery.form');
		$this->AssetCompress->script('jquery.plugins/jquery.qtip');
		$this->AssetCompress->script('jquery.plugins/jquery.cookie');
		$this->AssetCompress->script('jquery.plugins/jquery.wysiwyg');
		$this->AssetCompress->script('jquery.plugins/jquery.equalheights');
		$this->AssetCompress->script('jquery.plugins/jquery.fullcalendar');
		
		// CORE scripts
		$this->AssetCompress->script('functions');
		$this->AssetCompress->script('global');
		$this->AssetCompress->script('ui');
		$this->AssetCompress->script('form');
		$this->AssetCompress->script('navigation');
		
		// setup
		$this->Js->buffer('CORE.init();');
		$this->Js->buffer('CORE.register("notifications", "nav-notifications", "/notifications/quick")');
		echo $this->Js->writeBuffer();
		//echo $this->AssetCompress->includeAssets(Configure::read('debug') == 0);
		echo $this->AssetCompress->includeAssets(false);
		echo $scripts_for_layout;
	?>
</head>
<body>
	<div class="container_12" id="wrapper">
		<div class="container_12 clearfix" id="header">
			<div class="grid_10 main-nav-menu" id="primary">
				<ul>
					<li id="nav-home"><?php echo $this->Html->link('☻', array('controller' => 'pages', 'action' => 'display', 'home')); ?></li>
					<li id="nav-profile"><?php echo $this->Html->link('Profile', array('controller' => 'profiles', 'action' => 'view', 'User' => $activeUser['User']['id'])); ?>
						<ul>
							<li>
								<?php
										if (isset($activeUser['ImageIcon'])) {
											echo '<div class="profile-image">';
											$path = 's'.DS.$activeUser['ImageIcon']['dirname'].DS.$activeUser['ImageIcon']['basename'];
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
										echo '<div>';
										echo '</div>';
										echo '</div>';
									?>
								</div>
								<div style="clear:both" />
							</li>
							<li class="hover-row"><?php echo $this->Html->link('My Profile', array('controller' => 'profiles', 'action' => 'view', 'User' => $activeUser['User']['id'])); ?></li>
							<?php
							if ($activeUser['Profile']['leading'] > 0):
							?>
							<li class="hover-row"><?php echo $this->Html->link('Leader Dashboard', array('controller' => 'involvement_leaders', 'action' => 'dashboard', 'User' => $activeUser['User']['id'])); ?></li>
							<?php endif; ?>
						</ul>
					</li>
					<li id="nav-notifications">
						<?php 
						echo $this->requestAction('/notifications/quick', array('return'));
						?>
					</li>
					<li id="nav-ministries">
						<?php
						echo $this->Html->link('Ministries', array('controller' => 'ministries'));
						echo $this->element('menu'.DS.'campus', array(
							'campuses' => $campusesMenu,
							'cache' => '+1 day'
						), true);
						?>
					</li>
					<li id="nav-calendar"><?php echo $this->Html->link('Calendar', array('controller' => 'dates', 'action' => 'calendar', 'full')); ?>
						<ul>
							<li>
								<?php echo $this->element('calendar'); ?>
							</li>
							<li id="calendar-viewall">
								<?php echo $this->Html->link('View Full Size Calendar', array('controller' => 'dates', 'action' => 'calendar', 'full')); ?>
							</li>
						</ul>
					</li>
					<?php if (Configure::read()): ?>
					<li><?php echo $this->Html->link('Debugging', array('controller' => 'reports', 'action' => 'index')); ?>
						<ul><li><?php
					echo $this->Html->link('Report a bug on this page', array('controller' => 'sys_emails', 'action' => 'bug_compose'), array('rel' => 'modal-none'));
					echo $this->Html->link('View activity logs', array('controller' => 'logs', 'action' => 'index'), array('rel' => 'modal-none'));
					?></li></ul>
					</li>
					<?php endif; ?>
					<li id="nav-search">
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
								'value' => 'Search '.Core::read('general.site_name_tagless'),
								'size' => 30,
								'class' => 'search-out'
							));
							echo $this->Form->button(
								$this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-search')),
								array(
									'escape' => false
								)
							);
							echo $this->Form->end();
						?>
					</li>
				</ul>
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
			<?php
			echo $this->Html->image('logo-small.png');
			?>
		</div>
	</div>
	<?php
	// write any buffered scripts that were added in the layout
	echo $this->Js->writeBuffer();
	?>
</body>
</html>
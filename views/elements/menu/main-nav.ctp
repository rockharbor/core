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
			<?php 
			endif;
			if ($activeUser['Profile']['managing'] > 0):
			?>
			<li class="hover-row"><?php echo $this->Html->link('Manager Dashboard', array('controller' => 'ministry_leaders', 'action' => 'dashboard', 'User' => $activeUser['User']['id'])); ?></li>
			<?php endif; ?>
			<?php
			$link = $this->Permission->link('Admin Dashboard', array('controller' => 'users', 'action' => 'dashboard'));
			echo $link ? $this->Html->tag('li', $link, array('class' => 'hover-row')) : null;
			?>
			<?php
			echo $this->element('hooks', array(
				'hook' => 'root.profile'
			));
			?>
		</ul>
	</li>
	<li id="nav-notifications">
		<?php 
		echo $this->requestAction('/notifications/quick', array('return'));
		echo $this->element('hooks', array(
			'hook' => 'root.notifications'
		));
		?>
	</li>
	<li id="nav-ministries">
		<?php
		echo $this->Html->link('Ministries', array('controller' => 'ministries'));
		echo $this->element('menu'.DS.'campus', array(
			'campuses' => $campusesMenu,
			'cache' => '+1 day'
		), true);
		echo $this->element('hooks', array(
			'hook' => 'root.ministries'
		));
		?>
	</li>
	<li id="nav-calendar"><?php echo $this->Html->link('Calendar', array('controller' => 'dates', 'action' => 'calendar', 'full')); ?>
		<ul>
			<li>
				<?php echo $this->element('calendar', array('size' => 'mini')); ?>
			</li>
			<li id="calendar-viewall">
				<?php echo $this->Html->link('View Full Size Calendar', array('controller' => 'dates', 'action' => 'calendar', 'full')); ?>
			</li>
		</ul>
	</li>
	<?php
	echo $this->element('hooks', array(
		'hook' => 'root',
		'exclude' => array('profile', 'notifications', 'ministries')
	));
	?>
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
		echo $this->element('search', array(
			 'term' => Core::read('general.site_name_tagless')
		));
		?>
	</li>
</ul>
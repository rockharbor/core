<ul>
	<li id="nav-profile"><?php echo $this->Html->link('Profile', array('plugin' => false, 'controller' => 'profiles', 'action' => 'view')); ?>
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
			<li class="hover-row"><?php echo $this->Html->link('My Profile', array('plugin' => false, 'controller' => 'profiles', 'action' => 'view')); ?></li>
			<?php
			if ($activeUser['Profile']['leading'] > 0 || $activeUser['Profile']['managing'] > 0):
			?>
			<li class="hover-row"><?php echo $this->Html->link('Leader Dashboard', array('plugin' => false, 'controller' => 'leaders', 'action' => 'dashboard')); ?></li>
			<?php endif; ?>
			<?php
			$link = $this->Permission->link('Admin Dashboard', array('plugin' => false, 'controller' => 'users', 'action' => 'dashboard'));
			echo $link ? $this->Html->tag('li', $link, array('class' => 'hover-row')) : null;
			$link = $this->Permission->link('Search Users', array('plugin' => false, 'controller' => 'searches', 'action' => 'user'));
			echo $link ? $this->Html->tag('li', $link, array('class' => 'hover-row')) : null;
			$link = $this->Permission->link('Add User', array('plugin' => false, 'controller' => 'users', 'action' => 'add'), array('data-core-modal' => '{"update":false}'));
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
		echo $this->requestAction('/notifications/quick', array('return', 'renderAs' => 'ajax'));
		echo $this->element('hooks', array(
			'hook' => 'root.notifications'
		));
		?>
	</li>
	<li id="nav-campuses">
		<?php
		echo $this->Html->link('Campuses', array('plugin' => false, 'controller' => 'campuses'));
		echo $this->element('menu'.DS.'campus', array(
			'campuses' => $campusesMenu,
			'cache' => '+1 day'
		), true);
		echo $this->element('hooks', array(
			'hook' => 'root.ministries'
		));
		?>
	</li>
	<li id="nav-calendar"><?php echo $this->Html->link('Calendar', array('plugin' => false, 'controller' => 'dates', 'action' => 'calendar', 'full')); ?>
		<ul>
			<li>
				<?php echo $this->element('calendar', array('size' => 'mini')); ?>
			</li>
			<li class="bottom-link">
				<?php echo $this->Html->link('View Full Size Calendar', array('plugin' => false, 'controller' => 'dates', 'action' => 'calendar', 'full')); ?>
			</li>
		</ul>
	</li>
	<?php
	echo $this->element('hooks', array(
		'hook' => 'root',
		'exclude' => array('profile', 'notifications', 'ministries')
	));
	?>
	
	<li id="nav-search">
		<?php
		echo $this->element('search', array(
			 'term' => Core::read('general.site_name_tagless')
		));
		?>
	</li>
</ul>
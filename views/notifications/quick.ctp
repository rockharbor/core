<?php
echo $this->Html->link('Notifications', array('controller' => 'notifications', 'action' => 'index'), array('rel' => 'modal-notifications'));
if ($new > 0) {
	$count = $this->Html->tag('span', $new, array('class' => 'notification-count'));
	$bottom = $this->Html->tag('span', $this->Html->image('../assets/images/notification-flag-bottom.png'), array('class' => 'notification-flag'));
	echo $this->Html->tag('span', $count.$bottom, array('class' => 'notification-counter'));
}
?>
<ul>
	<?php
		foreach ($alerts as $alert) {
			$title = $this->Html->link($alert['Alert']['name'], array('controller' => 'alerts', 'action' => 'view', $alert['Alert']['id']), array('rel' => 'modal-notifications'));
			$desc = $this->Text->truncate($alert['Alert']['description'], 50);
			echo $this->Html->tag('li', $this->Html->tag('p', $title.' - '.$desc), array('class' => 'notification alert'));
		}
	?>
	<?php
		foreach ($notifications as $notification) {
			$class = $notification['Notification']['read'] ? 'notification-read' : 'notification-unread';
			echo '<li id="notification-'.$notification['Notification']['id'].'" class="notification"><p class="'.$class.'">';
			echo $this->Text->truncate($notification['Notification']['body'], 100, array('html' => true));
			echo '</p>';
			echo $this->Html->link('[X]', array(
				'controller' => 'notifications',
				'action' => 'delete',
				$notification['Notification']['id']
			), array(
				'class' => 'delete'
			));
			echo '</li>';
		}
		echo '<li id="notification-viewall">';
		echo $this->Html->link('View All Notifications', array('controller' => 'notifications', 'action' => 'index'), array('rel' => 'modal-notifications'));
		echo ' / ';
		echo $this->Html->link('Alerts', array('controller' => 'alerts', 'action' => 'history'), array('rel' => 'modal-notifications'));
		echo '</li>';
	?>
</ul>
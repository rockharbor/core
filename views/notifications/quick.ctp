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
			echo $this->Html->tag('li', $title.' - '.$desc, array('class' => 'notification alert'));
		}
	?>
	<?php
		foreach ($invitations as $invitation) {
			echo '<li class="notification invitation">';
			echo $invitation['Invitation']['body'];
			$icon = $this->element('icon', array('icon' => 'confirm'));
			$confirm = $this->Html->link($icon, array('controller' => 'invitations', 'action' => 'confirm', $invitation['Invitation']['id'], 1), array(
				'class' => 'confirm no-hover', 
				'escape' => false, 
				'title' => 'Confirm'
			));
			$icon = $this->element('icon', array('icon' => 'deny'));
			$deny = $this->Html->link($icon, array('controller' => 'invitations', 'action' => 'confirm', $invitation['Invitation']['id'], 0), array(
				'class' => 'deny no-hover', 
				'escape' => false, 
				'title' => 'Deny'
			));
			echo $this->Html->tag('span', $confirm.$deny, array('class' => 'actions'));
			echo '</li>';
		}
	?>
	<?php
		foreach ($notifications as $notification) {
			$class = $notification['Notification']['read'] ? 'read' : 'unread';
			echo '<li id="notification-'.$notification['Notification']['id'].'" class="notification '.$class.'">';
			echo $this->Text->truncate($notification['Notification']['body'], 100, array('html' => true));
			$icon = $this->element('icon', array('icon' => 'delete'));
			$delete = $this->Html->link($icon, array(
				'controller' => 'notifications',
				'action' => 'delete',
				$notification['Notification']['id']
			), array(
				'class' => 'delete no-hover',
				'escape' => false
			));
			echo $this->Html->tag('span', $delete, array('class' => 'actions'));
			echo '</li>';
		}
		echo '<li class="bottom-link">';
		echo $this->Html->link('View All Notifications', array('controller' => 'notifications', 'action' => 'index'), array('rel' => 'modal-notifications'));
		echo ' / ';
		echo $this->Html->link('Invitations', array('controller' => 'invitations', 'action' => 'index'), array('rel' => 'modal-notifications'));
		echo ' / ';
		echo $this->Html->link('Alerts', array('controller' => 'alerts', 'action' => 'history'), array('rel' => 'modal-notifications'));
		echo '</li>';
	?>
</ul>
<?php
echo $this->Html->link('Notifications', array('controller' => 'notifications', 'action' => 'index'), array('rel' => 'modal-notifications'));
if ($new > 0) {
	echo $this->Html->tag('span', $this->Html->tag('span', $new, array('class' => 'notification-count')), array('class' => 'notification-counter'));
}
?>
<ul>
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
		echo '</li>';
	?>
</ul>
<dl>
	<?php
	$link = $this->Html->link($user['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
	echo $this->Html->tag('dd', $link.$this->Formatting->flags('User', $user).'&nbsp;');
	echo $this->Html->tag('dt', 'Username:');
	echo $this->Html->tag('dd', $user['username'].'&nbsp;');
	echo $this->Html->tag('dt', 'Primary Email:');
	echo $this->Html->tag('dd', $this->Formatting->email($user['Profile']['primary_email'], $user['id']).'&nbsp;');
	if (!empty($user['Profile']['alternate_email_1'])) {
		echo $this->Html->tag('dt', 'Alternate Email:');
		echo $this->Html->tag('dd', $user['Profile']['alternate_email_1']);
	}
	if (!empty($user['Profile']['alternate_email_2'])) {
		echo $this->Html->tag('dt', 'Alternate Email:');
		echo $this->Html->tag('dd', $user['Profile']['alternate_email_2']);
	}
	echo $this->Html->tag('dt', 'Birthday:');
	echo $this->Html->tag('dd', $this->Formatting->date($user['Profile']['birth_date']).'&nbsp;');
	if (!empty($user['Address'])) {
		foreach ($user['Address'] as $address) {
			echo '<hr />';
			echo $this->Formatting->address($address);
		}
	}
	?>
</dl>
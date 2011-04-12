<dl>
	<?php
	echo $this->Html->tag('dd', $user['Profile']['name']);
	echo $this->Html->tag('dt', 'Username:');
	echo $this->Html->tag('dd', $user['username']);
	echo $this->Html->tag('dt', 'Birthday:');
	echo $this->Html->tag('dd', $this->Formatting->date($user['Profile']['birth_date']));
	echo '<hr />';
	foreach ($user['Address'] as $address) {
		$addr = $address['address_line_1'];
		if (!empty($address['address_line_2'])) {
			$addr .= '<br />'.$address['address_line_2'];
		}
		$addr .= '<br />';
		$addr .= $address['city'].', '.$address['state'].' '.$address['zip'];
		$icon = $this->element('icon', array('icon' => 'address'));
		echo $this->Html->tag('div', $icon.$addr);
	}
	?>
</dl>
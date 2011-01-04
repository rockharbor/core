<?php
if (isset($user['ImageIcon'])) {
	$path = 's'.DS.$user['ImageIcon']['dirname'].DS.$user['ImageIcon']['basename'];
	echo $this->Html->tag('div', $this->Media->embed($path, array('restrict' => 'image')), array('class' => 'autocomplete-image'));
}
?>
<div class="autocomplete-row">
<?php
echo $this->Html->tag('p', $this->Text->highlight($user['Profile']['name'], $query));
$email = $this->Text->highlight($user['Profile']['primary_email'], $query);
echo $this->Html->tag('p', $this->Html->link($email, array('controller' => 'sys_emails', 'action' => 'compose', 'model' => 'User', 'User' => $user['User']['id']), array('class' => 'icon-email', 'escape' => false)));
echo $this->Html->tag('p', $this->Formatting->phone($user['Profile']['cell_phone']));
?>
</div>
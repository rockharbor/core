<?php

echo $this->Html->link('Invite', 'javascript:;', array(
	'onclick' => 'inviteUser('.$result['User']['id'].');$(this).button({disabled:true});',
	'class' => 'button'
));

?>

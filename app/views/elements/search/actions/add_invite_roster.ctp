<?php

echo $this->Html->link('Invite', 'javascript:;', array(
	'onclick' => 'inviteRoster('.$result['Involvement']['id'].');$(this).button({disabled:true});',
	'class' => 'button'
));

?>

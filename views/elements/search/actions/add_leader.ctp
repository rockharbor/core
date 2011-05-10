<?php

echo $this->Html->link('Add', 'javascript:;', array(
	'onclick' => 'addLeader('.$result['User']['id'].');$(this).button({disabled:true});',
	'class' => 'button'
));

?>

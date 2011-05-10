<?php

echo $this->Html->link('Add', 'javascript:;', array(
	'onclick' => 'addToHH('.$result['User']['id'].');$(this).button({disabled:true});',
	'class' => 'button'
));

?>

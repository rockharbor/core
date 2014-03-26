<?php
$img = '<img src="'.DS.'assets'.DS.'images'.DS.'search.png'.'"/>';
echo $this->Html->tag('div', $img, array('class' => 'autocomplete-image'));
?>
<div class="autocomplete-row">
<?php
echo $this->Html->tag('p', 'Didn\'t find what you were looking for?');
echo $this->Html->tag('p', 'Click here to do a full search');
?>
</div>
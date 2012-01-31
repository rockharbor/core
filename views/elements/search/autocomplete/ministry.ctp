<?php
if (!empty($ministry['Image'])) {
	$path = 's'.DS.$ministry['Image']['dirname'].DS.$ministry['Image']['basename'];
	echo $this->Html->tag('div', $this->Media->embed($path, array('restrict' => 'image')), array('class' => 'autocomplete-image'));
}
?>
<div class="autocomplete-row">
<?php
echo $this->Html->tag('p', $this->Text->highlight($ministry['Ministry']['name'], $query));
echo $this->Html->tag('p', $this->Text->highlight($this->Text->excerpt($ministry['Ministry']['description'], $query, 20), $query));
?>
</div>
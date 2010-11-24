<?php
if (!empty($involvement['Image'])) {
	$path = 's'.DS.$involvement['Image'][0]['dirname'].DS.$involvement['Image'][0]['basename'];
	echo $this->Html->tag('div', $this->Media->embed($path, array('restrict' => 'image')), array('class' => 'autocomplete-image'));
}
?>
<div class="autocomplete-row">
<?php
echo $this->Html->tag('p', $this->Text->highlight($involvement['Involvement']['name'], $query));
echo $this->Html->tag('p', $this->Text->highlight($this->Text->excerpt($involvement['Involvement']['description'], $query, 20), $query));
?>
</div>
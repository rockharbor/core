<h2>Simple Search</h2>

<div class="searches">
<?php echo $this->Form->create(null, array(
	'default' => false,
	'url' => array(
		implode('/', array_merge($this->params['pass'], $this->params['named']))
	)
));
echo $this->element('search'.DS.strtolower($model).'_simple', array(), true);
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<div>
<?php
if (!empty($results)) {
?>
<?php
echo $this->element('search'.DS.strtolower($model).'_simple_results', compact('element', 'results'), true);
} elseif ($searchRan) {
?>
<p>No results</p>
<?php
}
?>
</div>
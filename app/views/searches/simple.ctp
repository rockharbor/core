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
	<h3>Results</h3>
<?php
echo $this->element('search'.DS.strtolower($model).'_simple_results', compact('actions', 'results'), true);
} elseif ($searchRan) {
?>
<h3>Results</h3>
<p>No results</p>
<?php
}
?>
</div>
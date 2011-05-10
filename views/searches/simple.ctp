<h1>Simple Search</h1>
<div class="searches content-box">
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

	<div>
	<?php
	if (!empty($results)) {
	?>
	<?php
	echo $this->element('search'.DS.strtolower($model).'_simple_results', compact('element', 'results'));
	} elseif ($searchRan) {
	?>
	<p>No results</p>
	<?php
	}
	?>
	</div>
	
</div>
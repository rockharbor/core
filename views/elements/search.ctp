<div class="search-form clearfix">
	<?php
	echo $this->Form->create('Search', array(
		'inputDefaults' => array(
			'div' => false
		),
		'url' => array(
			'plugin' => false, 
			'controller' => 'searches',
			'action' => 'index'
		)
	));
	if (isset($model) && isset($model_id)) {
		echo $this->Form->hidden('Search.'.$model.'.id', array(
			 'value' => $model_id
		));
	}
	if (!isset($term)) {
		$term = null;
	}
	echo $this->Form->input('query', array(
		'label' => false,
		'value' => 'Search '.$term,
		'size' => 18,
		'class' => 'search-out',
		'id' => uniqid('SearchQuery')
	));
	echo $this->Form->button(
		$this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-search')),
		array(
			'escape' => false
		)
	);
	echo $this->Form->end();
	?>
</div>
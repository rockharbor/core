<h2>Search</h2>

<div class="involvements">
<?php echo $this->Form->create('Search', array(
	'action' => 'involvement',
	'default' => false
));?>
	<fieldset>
 		<legend>Search Involvement Opportunities</legend>
	<?php
		echo $this->Form->input('Search.operator', array(
			'type' => 'select',
			'options' => array(
				'AND' => 'Match all',
				'OR' => 'Match any'
			)
		));
		echo $this->Form->input('Involvement.name');
		echo $this->Form->input('Involvement.description');
		echo $this->Form->input('Ministry.name', array(
			'label' => 'Ministry'
		));
		echo $this->Form->input('Involvement.involvement_type_id', array(
			'multiple' => 'checkbox'
		));
	?>
	</fieldset>
<?php
$defaultSubmitOptions['update'] = '#involvement-results';
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<div id="involvement-results">
</div>
<h2>Search</h2>

<div class="ministries">
<?php echo $this->Form->create('Search', array(
	'action' => 'ministry',
	'default' => false
));?>
	<fieldset>
 		<legend>Search Ministries</legend>
	<?php
		echo $this->Form->input('Search.operator', array(
			'type' => 'select',
			'options' => array(
				'AND' => 'Match all',
				'OR' => 'Match any'
			)
		));
		echo $this->Form->input('Ministry.name');
		echo $this->Form->input('Ministry.description');
		echo $this->Form->input('Ministry.campus_id', array(
			'empty' => true
		));
	?>
	</fieldset>
<?php
$defaultSubmitOptions['update'] = '#ministry-results';
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<div id="ministry-results">
</div>
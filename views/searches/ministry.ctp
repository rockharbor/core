<h1>Search Ministries</h1>
<div class="ministries content-box">
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
$defaultSubmitOptions['success'] = '$("#ministry-results").html(data);';
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();
$url = $this->here; // doesn't matter, as the updateable is only needed for the div (pagination)
$this->Js->buffer('CORE.register("results", "ministry-results", "'.$url.'");');
?>
	<div id="ministry-results">
	</div>
</div>
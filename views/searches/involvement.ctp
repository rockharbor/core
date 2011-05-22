<h1>Search</h1>
<div class="involvements content-box">
<?php echo $this->Form->create('Search', array(
	'action' => 'involvement',
	'default' => false
));?>
	<div class="clearfix">
		<fieldset class="grid_5 alpha">
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
			echo $this->Form->hidden('Involvement.private', array('value' => 0));
			echo $this->Form->hidden('Involvement.active', array('value' => 1));
			echo $this->Form->hidden('Involvement.passed', array('value' => 0));
		?>
		</fieldset>
		<fieldset class="grid_5 omega">
			<legend></legend>
			<?php
			echo $this->Form->input('Ministry.name', array(
				'label' => 'Ministry'
			));
			echo $this->Form->input('Involvement.involvement_type_id', array(
				'multiple' => 'checkbox'
			));
			?>
		</fieldset>
	</div>
<?php
$defaultSubmitOptions['success'] = '$("#involvement-results").html(data);';
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();
$url = $this->here; // doesn't matter, as the updateable is only needed for the div (pagination)
$this->Js->buffer('CORE.register("results", "involvement-results", "'.$url.'");');
?>
	
	<div id="involvement-results">
	</div>
</div>

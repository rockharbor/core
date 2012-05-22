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
			echo $this->Form->input('Involvement.name');
			echo $this->Form->input('Involvement.description');
			if ($inactive) {
				echo $this->Form->input('Involvement.inactive', array(
					'type' => 'checkbox'
				));
				echo $this->Form->input('Involvement.previous', array(
					'type' => 'checkbox'
				));
			}
			if ($private) {
				echo $this->Form->input('Involvement.private');
			}
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
	
	<div id="involvement-results" class="parent">
	</div>
</div>

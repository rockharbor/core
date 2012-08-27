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
			echo $this->Form->input('Ministry.name', array(
				'label' => 'Ministry'
			));
			echo $this->Form->input('Involvement.involvement_type_id', array(
				'multiple' => 'checkbox'
			));
			?>
		</fieldset>
		<fieldset class="grid_5 alpha">
				<legend>Location</legend>
		<?php
			echo $this->Form->input('Address.city');
			echo $this->Form->input('Address.state', array(
				'options' => $this->SelectOptions->states,
				'empty' => true
			));
			echo $this->Form->input('Address.zip');
			echo $this->Form->input('Distance.distance_from', array(
				'after' => '<p>(try: orange, ca)</p>'
			));
			echo $this->Form->input('Distance.distance', array(
				'type' => 'select',
				'options' => array(	
					'5' => '5 miles',
					'10' => '10 miles',
					'15' => '15 miles',
					'25' => '25 miles'
				),
				'empty' => false
			));
		?>
		</fieldset>
	</div>
<?php
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();

if (!empty($results)) {

?>
<h3>Results</h3>
	<p>Sort by: <?php echo $this->Paginator->sort('name'); ?> / <?php echo $this->Paginator->sort('description'); ?> 
	/ <?php echo $this->Paginator->sort('Type', 'InvolvementType.name'); ?> / <?php echo $this->Paginator->sort('Ministry', 'Ministry.name'); ?>
	</p>
	<div class="clearfix">
<?php	
	$i = 0;
	foreach ($results as $result):
		$class = ($i % 2 == 0) ? 'alpha' : 'omega';
		echo $this->element('involvement_column', array('involvement' => $result, 'class' => $class));
	endforeach;
?>
	</div>
<?php
	echo $this->element('pagination');
} else {
?>
<h3>Results</h3>
<p>No results</p>
<?php 
}
?>
</div>

<?php
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

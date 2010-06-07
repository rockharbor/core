<div class="navigation">
<h2>Ministry Menu</h2>
<?php 
echo $this->Tree->generate($ministries, array(
	'element' => 'menu'.DS.'menu_item'
));
?>
</div>
<div class="simple_lists">
<h2><?php echo Inflector::humanize($modelKey);?></h2>
	<dl>
	<?php 
	
	$i = 0; 
	
	foreach ($schema as $field => $attrs) {		
		if ($i % 2 == 0) {
			$class = ' class="altrow"';
		} else {
			$class = '';
		}
		// ignore certain fields
		if (!in_array($field, array('created', 'modified'))) {
			echo '<dt'.$class.'>'.Inflector::humanize($field).'</dt>';
			$varName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $field)));
			if (isset(${$varName})) {
				echo '<dd'.$class.'>'.${$varName}[$result[$model][$field]].'</dd>';
			} else {
				echo '<dd'.$class.'>'.$result[$model][$field].'</dd>';
			}
		}
		
		$i++;
	}
	?>
	</dl>
</div>
<div>
	<?php echo $this->Js->link('Edit', array('action' => 'edit', $result[$model]['id']), array(
		'update' => '#content',
		'class' => 'button'
	)); ?>
</div>
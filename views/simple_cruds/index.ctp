<?php
$this->Paginator->options(array(
    'update' => '#content', 
    'evalScripts' => true
));
?>

<div class="simple_lists index">
	<h2><?php echo Inflector::pluralize(Inflector::humanize($modelKey)); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<?php
		foreach ($schema as $field => $attrs) {
			echo '<th>'.$this->Paginator->sort($field).'</th>';		
		}
		?>
		<th class="actions">Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($results as $result):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<?php
		foreach ($schema as $field => $attrs) {
			$varName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $field)));
			if (isset(${$varName})) {
				echo '<td>'.${$varName}[$result[$model][$field]].'</td>';
			} else {				
				echo '<td>'.$result[$model][$field].'</td>';		
			}
		}
		?>
		<td class="actions">
			<?php echo $this->Html->link('View', array('action' => 'view', $result[$model]['id']), 
				array(
					'rel'=>'modal-content'
				)
			); ?>
			<?php echo $this->Html->link('Edit', array('action' => 'edit', $result[$model]['id']), 
				array(
					'rel'=>'modal-content'
				)
			); ?>
			<?php 
			echo $this->Html->link('Delete', array('action' => 'delete', $result[$model]['id']), array(
					'id'=>'delete_btn_'.$i
			));
			?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('New '.Inflector::humanize($modelKey), array('action' => 'add'), array(
			'rel' => 'modal-content'
		)); ?></li>
	</ul>
</div>

<?php
while ($i > 0) {
	$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'","Are you sure you want to delete this '.Inflector::humanize($modelKey).'?", {update:"content"});');
	$i--;
}
?>
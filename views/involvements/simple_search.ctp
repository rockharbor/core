<h2>Simple Search</h2>

<div class="users">
<?php echo $this->Form->create('Involvement', array(
	'default' => false, 
	'url' => array(
		$filters
	)
));?>
	<fieldset>
 		<legend>Search Involvement Opportunities</legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<div>

<?php
if (!empty($results)) {

$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h3>Results</h3>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
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
			<td><?php echo $this->Formatting->flags('Involvement', $result).$result['Involvement']['name']; ?></td>
			<td class="actions"><?php 
				if (!empty($actions)) {
					foreach ($actions as $name => $jsfunc) {
						echo $this->Html->link($name, 'javascript:;', array(
							'onclick' => $jsfunc.'('.$result['Involvement']['id'].');$(this).addClass("disabled");$(this).removeAttr("onclick");'
						));
					}
				}
			?></td>
		</tr>
<?php	
	endforeach;
?>
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
<?php
} elseif ($searchRan) {
?>
<h3>Results</h3>
<p>No results</p>
<?php 
}
?>
</div>
<?php
$this->Paginator->options(array(
    'update' => '#content', 
    'evalScripts' => true
));
?>
<div class="appSettings">
	<h2><?php __('App Settings');?></h2>
	<p>App settings are cached and may take a moment to reflect your changes.</p>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>			
			<th><?php echo $this->Paginator->sort('value');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($appSettings as $appSetting):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo Inflector::humanize($appSetting['AppSetting']['name']); ?>&nbsp;</td>
		<td><?php echo $appSetting['AppSetting']['description']; ?>&nbsp;</td>		
		<td><?php 
		
		if (empty($appSetting['AppSetting']['model'])) {
			echo $appSetting['AppSetting']['value']; 
		} else {
			if (!empty($appSetting['AppSetting']['value'])) {
				echo ${$appSetting['AppSetting']['model'].'Options'}[$appSetting['AppSetting']['value']];
			}
		}
		
		?>&nbsp;</td>
		<td><?php echo $appSetting['AppSetting']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Js->link('Edit', array('action' => 'edit', $appSetting['AppSetting']['id']),
				array(
					'rel'=>'modal-content'
				)
			); ?>
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
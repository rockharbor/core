<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Regions</h1>
<div id="regions-index" class="regions index">
	<table class="datatable">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th>Zipcodes</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<?php
	$i = 0;
	foreach ($regions as $region):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' altrow';
		}
	?>
	<tr class="<?php echo $class;?>">
		<td width="100"><?php echo $region['Region']['name']; ?></td>
		<td><?php
			foreach ($region['Zipcode'] as $zipcode) {
				echo '<div class="core-iconable" style="float:left;padding-right: 10px;margin-right:5px;height: 20px">'.$zipcode['zip'].'&nbsp;';
				$icon = $this->element('icon', array('icon' => 'delete'));
				echo '<div class="core-icon-container">';
				echo $this->Js->link(
					$icon,
					array('controller' => 'zipcodes', 'action' => 'delete', $zipcode['id']),
					array('complete' => 'CORE.update(CORE.getUpdateableParent("regions-index").updateable)', 'escape' => false, 'class' => 'no-hover')
				);
				echo '</div>';
				echo '</div>';
			}
			echo '<br clear="all" />';
			echo '<div>';
			$icon = $this->element('icon', array('icon' => 'add'));
			echo $icon.$this->Html->link(' Add zipcode', array('controller' => 'zipcodes', 'action' => 'add', 'Region' => $region['Region']['id']), array('rel' => 'modal-parent'));
			echo '</div>';
			?></td>
		<td>
			<span class="core-icon-container">
				<?php
				$icon = $this->element('icon', array('icon' => 'edit'));
				echo $this->Html->link($icon, array('action' => 'edit', $region['Region']['id']), array('rel' => 'modal-parent', 'title' => 'Edit Region', 'escape' => false, 'class' => 'no-hover'));
				$icon = $this->element('icon', array('icon' => 'delete'));
				echo $this->Html->link($icon, array('action' => 'delete', $region['Region']['id']), array('title' => 'Delete Region', 'id' => 'delete-region-'.$region['Region']['id'], 'escape' => false, 'class' => 'no-hover'));
				$this->Js->buffer('CORE.confirmation("delete-region-'.$region['Region']['id'].'", "Are you sure you want to delete this region and all its zipcodes?", {update:"parent"})');
				?>
			</span>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<?php echo $this->element('pagination'); ?>
</div>
<?php echo $this->Html->link('New Region', array('action' => 'add'), array('rel' => 'modal-parent', 'class' => 'button')); 
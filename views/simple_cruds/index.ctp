<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1><?php echo Inflector::pluralize(Inflector::humanize($modelKey)); ?></h1>
<div class="simple_lists index">
	<table class="datatable">
		<thead>
			<tr>
				<?php
				foreach ($schema as $field => $attrs) {
					if (in_array($field, array('id', 'created'))) {
						continue;
					}
					echo '<th>'.$this->Paginator->sort($field).'</th>';
				}
				?>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($results as $result):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' altrow';
				}
			?>
			<tr class="core-iconable<?php echo $class;?>">
				<?php
				foreach ($schema as $field => $attrs) {
					if (in_array($field, array('id', 'created'))) {
						continue;
					}
					$varName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $field)));
					if (isset(${$varName})) {
						echo '<td>'.${$varName}[$result[$model][$field]].'</td>';
					} else {
						echo '<td>'.$result[$model][$field].'</td>';
					}
				}
				?>
				<td>
					<span class="core-icon-container">
					<?php
					$icon = $this->element('icon', array('icon' => 'edit'));
					echo $this->Html->link($icon, array('action' => 'edit', $result[$model]['id']), array('rel' => 'modal-parent', 'class' => 'no-hover', 'escape' => false));
					$icon = $this->element('icon', array('icon' => 'delete'));
					echo $this->Html->link($icon, array('action' => 'delete', $result[$model]['id']), array('id' => 'delete-btn-'.$result[$model]['id'], 'class' => 'no-hover', 'escape' => false));
					$this->Js->buffer('CORE.confirmation("delete-btn-'.$result[$model]['id'].'", "Are you sure you want to delete this '.Inflector::humanize($modelKey).'?", {update:"parent"});');
					?>
					</span>
				</td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
<?php echo $this->element('pagination'); ?>
</div>
<?php echo $this->Html->link('New '.Inflector::humanize($modelKey), array('action' => 'add'), array('rel' => 'modal-parent', 'class' => 'button')); ?>

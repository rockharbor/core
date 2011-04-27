<?php
if (!isset($class)) {
	$class = null;
}
?>
<div class="involvement-column grid_5 <?php echo $class; ?>">
	<div class="involvement-header"><?php echo $this->Html->link(html_entity_decode($involvement['Involvement']['name']), array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])).$this->Formatting->flags('Involvement', $involvement); ?></div>
	<div class="involvement-details">
		<?php
		// determine where the associated info lies (linkable puts it in the same level, containable puts it within the key)
		$parent = isset($involvement['Ministry']['Campus']) ? $involvement['Ministry'] : $involvement;
		echo $this->Html->link(html_entity_decode($parent['Campus']['name']), array('controller' => 'campuses', 'action' => 'view', 'Campus' => $parent['Campus']['id']));
		echo ' > ';
		if (!empty($parent['ParentMinistry'])) {
			echo $this->Html->link(html_entity_decode($parent['ParentMinistry']['name']), array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $parent['ParentMinistry']['id']));
			echo ' > ';
		}
		echo $this->Html->link(html_entity_decode($involvement['Ministry']['name']), array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['id']));
		echo '<hr>';
		echo $this->Text->truncate(html_entity_decode($involvement['Involvement']['description']), 250, array('html' => true));
		?>
	</div>
	<div class="involvement-date">
		<?php
		if (!empty($involvement['dates'])) {
			echo $this->Html->tag('div', date('m/d/y', strtotime($involvement['dates'][0]['Date']['start_date'])));
			echo $this->Html->tag('div', date('g:i', strtotime($involvement['dates'][0]['Date']['start_time'])), array('class' => date('a', strtotime($involvement['dates'][0]['Date']['start_time']))));
		}
		echo $this->Html->tag('div', $this->Html->link('Get Involved', array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id']), array('class' => 'button')));
		?>
	</div>
</div>
<ul>
<?php
$firstCampus = $campuses[0];
echo '<li class="campuses toggleset">';
$radios = Set::combine($campuses, '/Campus/id', '/Campus/name');
echo $this->Form->radio(null,
	$radios,
	array(
		'legend' => false,
		'value' => $firstCampus['Campus']['id'],
	)
);
echo '</li>';

foreach ($campuses as $campus) {
	echo '<li class="campus" id="campus-'.$campus['Campus']['id'].'">';
	$half = round(count($campus['Ministry'])/2);
	$i = 0;
	$h = 0;
	while ($i < 2) {
		echo '<ul class="ministry-column">';
		for($h; $h<($half*($i+1)) && $h < count($campus['Ministry']); $h++) {
			echo '<li class="ministry">';
			echo $this->Html->link($campus['Ministry'][$h]['name'], array('plugin' => false, 'controller' => 'ministries', 'action' => 'view', 'Ministry' => $campus['Ministry'][$h]['id']), array('class' => 'parent-ministry', 'escape' => true));
			$childrenLinks = array();
			foreach ($campus['Ministry'][$h]['ChildMinistry'] as $childMinistry) {
				$childrenLinks[] = $this->Html->link($childMinistry['name'], array('plugin' => false, 'controller' => 'ministries', 'action' => 'view', 'Ministry' => $childMinistry['id']), array('class' => 'child-ministry', 'escape' => true));
			}
			if (count($childrenLinks) > 4) {
				$childrenLinks[] = $this->Html->link('more...', array('plugin' => false, 'controller' => 'ministries', 'action' => 'view', 'Ministry' => $campus['Ministry'][$h]['id']), array('class' => 'child-ministry', 'escape' => true));
			}
			if (count($childrenLinks) > 0) {
				echo $this->Text->toList($childrenLinks);
			}
			echo '</li>';
		}
		echo '</ul>';
		$i++;
	}
	echo $this->Html->tag('span', '&nbsp;', array('class' => 'clearfix'));
	$link = $this->Html->link('View '.$campus['Campus']['name'].' campus page', array('plugin' => false, 'controller' => 'campuses', 'action' => 'view', 'Campus' => $campus['Campus']['id']));
	echo $this->Html->tag('div', $link, array('class' => 'bottom-link'));
	echo '</li>';
}
?>
</ul>
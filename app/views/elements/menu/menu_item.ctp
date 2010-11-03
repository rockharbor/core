<?php
$class = empty($data['Ministry']['parent_id']) ? 'parent' : 'child';
echo $html->link($data['Ministry']['name'], array('action' => 'view', 'Ministry' => $data['Ministry']['id']), array('class' => $class));
?>
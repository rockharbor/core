<?php echo $leaver['Profile']['name']; ?> has been removed from <strong><?php echo $this->Html->link($involvement['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])); ?></strong>.
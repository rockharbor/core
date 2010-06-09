<?php
echo $this->requestAction(array('controller' => 'errors', 'action' => 'index', 'plugin' => 'core_debug_panels'), array('return', 'bare' => true));
?>
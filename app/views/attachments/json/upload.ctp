<?php
Configure::write('debug', 0);
// pass validation errors back to js
echo $this->Js->object($this->validationErrors);

?>
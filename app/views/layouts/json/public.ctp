<?php

Configure::write('debug', 0);

header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");

echo $content_for_layout;

?>
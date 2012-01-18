<?php

Configure::write('debug', 0);

header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");

if (isset($this->params['url']['callback'])) {
	$content_for_layout = $this->params['url']['callback'].'('.$content_for_layout.');';
}
echo $content_for_layout;
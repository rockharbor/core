<?php

Configure::write('debug', 0);

header("Content-type:application/vnd.ms-excel"); 
header("Content-disposition:attachment;filename=".$title_for_layout.".csv");

echo $content_for_layout;

?>
<?php

$readable = $this->Formatting->readableDate($date);

echo json_encode(compact('date', 'readable'));
<?php
$this->Report->set($results);
$this->Report->squashFields($squashed);
$this->Report->headerAliases($aliases);
$this->Report->multipleRecords($multiples);
$this->Csv->addRow($this->Report->createHeaders($models));

$rows = $this->Report->getResults();
foreach ($rows as $row) {
	$this->Csv->addRow($row);
}

echo $this->Csv->render(false);

?>
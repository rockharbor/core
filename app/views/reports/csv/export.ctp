<?php
// header row
$header = array();

/**
 * Creates header array from recursive list of models => fields
 *
 * @param array $m The models-field keys
 * @return array Headers
 */
function createHeaders($m) {
	static $hdr = array();
	
	foreach ($m as $model => $fields) {
		if (is_array($fields)) {
			createHeaders($fields);
		} else {
			$hdr[] = Inflector::humanize($model);
		}
	}
	
	return $hdr;
}
$header = createHeaders($models);

foreach ($header as $headerRow) {
	$this->Csv->addField($headerRow);
}
$this->Csv->endRow();


/**
 * Creates cell array from flat results
 *
 * @param array $paths The paths to pull
 * @param array $flatresults Flattened results
 * @param integer $number The current result number to pull
 * @return array Cells
 */
function createCells($paths, $flatresults, $number) {
	$clls = array();
	foreach ($paths as $path) {
		$clls[] =  $flatresults[$number.'.'.$path];
	}
	
	return $clls;
}

// counter
$c = 0;
// total results
$t = count($results);
// flatten the results
$results = Set::flatten($results); 
// create paths from chosen fields
$paths = array_keys(Set::flatten($models));

while ($c < $t) {
	$cells = createCells($paths, $results, $c);
	foreach ($cells as $cell) {
		$this->Csv->addField($cell);
	}
	$this->Csv->endRow();
	$c++;
}

echo $this->Csv->render(false);


?>
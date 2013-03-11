<?php

class CsvHelper extends AppHelper {

	public $delimiter = ',';
	public $enclosure = '"';
	public $filename = 'Export.csv';
	public $line = array();
	public $buffer;

	public function CsvHelper() {
		$this->clear();
	}

	public function clear() {
		$this->line = array();
		$this->buffer = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
	}

	public function addField($value) {
		$this->line[] = $value;
	}

	public function endRow() {
		$this->addRow($this->line);
		$this->line = array();
	}

	public function addRow($row) {
		fputcsv($this->buffer, $row, $this->delimiter, $this->enclosure);
	}

	public function renderHeaders() {
		header("Content-type:application/vnd.ms-excel");
		header("Content-disposition:attachment;filename=".$this->filename);
	}

	public function setFilename($filename) {
		$this->filename = $filename;
		if (strtolower(substr($this->filename, -4)) != '.csv') {
			$this->filename .= '.csv';
		}
	}

	public function render($outputHeaders = true, $to_encoding = null, $from_encoding = "auto") {
		if ($outputHeaders) {
			if (is_string($outputHeaders)) {
				$this->setFilename($outputHeaders);
			}
			$this->renderHeaders();
		}
		rewind($this->buffer);
		$output = stream_get_contents($this->buffer);
		if ($to_encoding) {
			$output = mb_convert_encoding($output, $to_encoding, $from_encoding);
		}
		return $this->output($output);
	}
}


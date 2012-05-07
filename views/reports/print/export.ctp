<?php
$this->Report->set($results);
$this->Report->squashFields($squashed);
$this->Report->headerAliases($aliases);
?>
<table>
	<thead>
		<?php	echo $this->Html->tableHeaders($this->Report->createHeaders($models)); ?>
	</thead>
	<tbody>
		<?php
		$rows = $this->Report->getResults();
		foreach ($rows as $row) {
			echo $this->Html->tableCells($row);
		}
		?>
	</tbody>
</table>
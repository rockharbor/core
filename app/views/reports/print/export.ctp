<table>
	<thead>
		<?php	echo $this->Html->tableHeaders($this->Report->createHeaders($models)); ?>
	</thead>
	<tbody>
		<?php
		$rows = $this->Report->getResults($results);
		foreach ($rows as $row) {
			echo $this->Html->tableCells($row);
		}
		?>
	</tbody>
</table>
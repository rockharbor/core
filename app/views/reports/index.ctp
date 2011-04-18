<h1>Ministry Report</h1>
<div id="report-form" class="clearfix">
	<?php
	echo $this->Form->create('Ministry', array(
		'default' => false,
		'inputDefaults' => array(
			'empty' => true
		)
	));
	?>
	<fieldset>
		<legend>Filter by</legend>
		<?php
		echo $this->Form->input('campus_id', array(
			'after' => ' OR '
		));
		echo $this->Form->input('id', array(
			'label' => 'Ministry',
			'options' => $ministries
		));
		?>
	</fieldset>
	<?php
	echo $this->Js->submit('Get Report', $defaultSubmitOptions);
	echo $this->Form->end();
	?>
</div>
<hr />
<div id="ministry-reports" class="reports clearfix">
	<h3>Ministries</h3>
	<div class="grid_5 alpha">
		<div class="report clearfix">
			<div class="chart">
			<?php
			$d1 = floor($ministryCounts['active']/$ministryCounts['total']*100);
			$d2 = 100-$d1;
			echo $this->Charts->draw('pie', array(
				'data' => array($d1, $d2),
				'size' => array(100, 100),
				'color' => array(
					'series' => array(
						array('45da88', '444445')
					)
				),
				'axes' => false
			));
			?>
			</div>
			<div class="data">
				<span class="font-large"><span class="green"><?php echo number_format($ministryCounts['active']); ?></span>/<?php echo number_format($ministryCounts['total']); ?></span>
				<p>Active Ministries</p>
			</div>
		</div>
		<div class="report clearfix">
			<div class="chart">
			<?php
			$d1 = floor($ministryCounts['private']/$ministryCounts['total']*100);
			$d2 = 100-$d1;
			echo $this->Charts->draw('pie', array(
				'data' => array($d1, $d2),
				'size' => array(100, 100),
				'color' => array(
					'series' => array(
						array('45da88', '444445')
					)
				),
				'axes' => false
			));
			?>
			</div>
			<div class="data">
				<span class="font-large"><span class="green"><?php echo number_format($ministryCounts['private']); ?></span>/<?php echo number_format($ministryCounts['total']); ?></span>
				<p>Private Ministries</p>
			</div>
		</div>
	</div>
	<?php if (empty($this->data)): ?>
	<div class="grid_5 omega">
		<div class="report clearfix">
			<div class="chart">
			<?php
			$d1 = floor($userCounts['active']/$userCounts['total']*100);
			$d2 = 100-$d1;
			echo $this->Charts->draw('pie', array(
				'data' => array($d1, $d2),
				'size' => array(100, 100),
				'color' => array(
					'series' => array(
						array('45da88', '444445')
					)
				),
				'axes' => false
			));
			?>
			</div>
			<div class="data">
				<span class="font-large"><span class="green"><?php echo number_format($userCounts['active']); ?></span>/<?php echo number_format($userCounts['total']); ?></span>
				<p>Active Users</p>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="grid_3 omega<?php if (empty($this->data)) { echo " border-right"; } ?>">
		<span class="font-large"><?php echo number_format($userCounts['involved']); ?></span>
		<p>Unique Users Involved</p>
	</div>
	<?php if (empty($this->data)): ?>
	<div class="grid_2 omega">
		<span class="font-large"><?php echo number_format($userCounts['logged_in']); ?></span>
		<p>Users Logged In Today</p>
	</div>
	<?php endif; ?>
</div>
<hr />
<div class="reports clearfix">
	<h3>Involvement Opportunities</h3>
	<div class="report">
		<?php
		$total = 0;
		$data = array();
		foreach ($involvementTypes as $type) {
			$total += $involvementCounts[$type]['total'];
		}
		foreach ($involvementTypes as $type) {
			$data[] = floor($involvementCounts[$type]['total']/$total*100);
		}
		echo $this->Charts->draw('bar', array(
			'spacing' => array(
				'width' => 30,
				'padding' => 5
			),
			'data' => $data,
			'size' => array(790, 40*count($involvementTypes)),
			'color' => array(
				'series' => array(
					'45da88|444445'
				)
			),
			'labels' => $involvementTypes,
			'axes' => array(
				'y' => array_reverse($involvementTypes)
			)
		));
		?>
	</div>
	<?php foreach ($involvementTypes as $type): ?>
	<h3><?php echo Inflector::pluralize($type); ?></h3>
	<div class="report clearfix box">
		<div class="grid_3"><span class="font-large"><?php echo number_format($involvementCounts[$type]['involved']); ?></span><p>Unique Users Involved</p></div>
		<div class="grid_2 omega"><span class="font-large"><?php echo number_format($involvementCounts[$type]['leaders']); ?></span><p>Leaders</p></div>
		<div class="grid_4 omega">
			<div class="chart">
			<?php
			if ($involvementCounts[$type]['total'] == 0) {
				$d1 = 0;
				$d2 = 100;
			} else {
				$d1 = floor($involvementCounts[$type]['active']/$involvementCounts[$type]['total']*100);
				$d2 = 100-$d1;
			}
			
			echo $this->Charts->draw('pie', array(
				'data' => array($d1, $d2),
				'size' => array(100, 100),
				'color' => array(
					'series' => array(
						array('45da88', '444445')
					),
					'background' => 'f1f1f1'
				),
				'axes' => false
			));
			?>
			</div>
			<div class="data">
				<span class="font-large"><span class="green"><?php echo number_format($involvementCounts[$type]['active']); ?></span>/<?php echo number_format($involvementCounts[$type]['total']); ?></span>
				<p>Active <?php echo Inflector::pluralize($type); ?></p>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
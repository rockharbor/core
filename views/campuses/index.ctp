<h1><?php __('Campuses');?></h1>
<div class="campuses index content-box clearfix">
	<table>
	<?php foreach ($campusesMenu as $campus): ?>
	<tr>
		<td style="vertical-align: top; padding-bottom: 10px;">
			<div>
				<?php
				echo $this->Html->tag('h2', $campus['Campus']['name'], array('style' => 'display: inline; margin-right: 10px;'));
				?>
			</div>
		<?php
		$col1links = array();
		$numPerCol = ceil(count($campus['Ministry']) / 2);
		for ($m = 0; $m < $numPerCol; $m++) {
			$parentMinistry = $campus['Ministry'][$m];
			$link = $this->Html->link($parentMinistry['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $parentMinistry['id']));
			$col1links[] = $link;
		}
		$col2links = array();
		for ($m = $numPerCol; $m < count($campus['Ministry']); $m++) {
			$parentMinistry = $campus['Ministry'][$m];
			$link = $this->Html->link($parentMinistry['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $parentMinistry['id']));
			$col2links[] = $link;
		}
		?>
			<table>
				<tr>
					<td style="width: 49%; padding-right: 1%;">
						<ul>
						<?php 
						foreach ($col1links as $link) {
							echo $this->Html->tag('li', $link, array('style' => 'list-style: none; margin: 0;'));
						}
						?>
						</ul>
					</td>
					<td style="width: 49%; padding-left: 1%;">
						<ul>
						<?php 
						foreach ($col2links as $link) {
							echo $this->Html->tag('li', $link, array('style' => 'list-style: none; margin: 0;'));
						}
						?>
						</ul>
					</td>
				</tr>
			</table>
		</td>
		<td style="vertical-align: top; width: 200px; padding-left: 10px; padding-bottom: 10px;">
			<?php
			echo $this->element('search', array(
				'model' => 'Campus',
				'model_id' => $campus['Campus']['id'],
				'term' => $campus['Campus']['name']
			));
			echo '<hr />';
			$link = $this->Html->link('Browse '.$campus['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $campus['Campus']['id']));
			echo $this->Html->tag('p', $link);
			$link = $this->Html->link($campus['Campus']['name'].' Calendar', array('controller' => 'dates', 'action' => 'calendar', 'full', 'Campus' => $campus['Campus']['id']));
			echo $this->Html->tag('p', $link);
			?>
		</td>
		<td style="vertical-align: top; width: 200px; padding-left: 10px; padding-bottom: 10px;">
			<?php
			echo $this->element('calendar', array('filters' => array(
				'Campus' => $campus['Campus']['id']
			)));
			?>
		</td>
	</tr>
	<tr><td colspan="3" style="padding-bottom: 10px;"><hr /></td></tr>
	<?php endforeach; ?>
	</table>
	<ul class="core-admin-tabs">
		<?php
		$link = $this->Permission->link('New Campus', array('action' => 'add'), array('data-core-modal' => 'true', 'class' => 'button')); 
		echo $link ? $this->Html->tag('li', $link) : null;
		?>
	</ul>
</div>

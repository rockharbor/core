<div class="pagination clearfix">
	&nbsp;
	<?php
	$params = $this->Paginator->params();
	$pages = $params['pageCount'];
	$records = $params['count'];
	if ($pages == 1) {
		if ($records == 0) {
			echo $this->Html->tag('p', 'No records found.', array('style' => 'float:left'));
		}
	} else {
	?>
		<span style="float:left">
		<?php
		echo $this->Paginator->prev('Prev', array('class' => 'button'), null, array('class' => 'button disabled'));
		echo $this->Paginator->next('Next', array('class' => 'button'), null, array('class' => 'button disabled'));
		?></span>
		<span style="float:right;line-height:30px">
			<?php
			$select = '%count%';
			if ($pages > 3) {
				$range = range(1, $pages);
				$select = $this->Form->select('jump', array_combine($range, $range), $params['page'], array(
					'empty' => false
				));
			}
			echo $this->Paginator->counter(array('format' => "Page: $select of %pages% ($records total records)"));
			?>
		</span>
	<?php
	}
	?>
</div>
<h1>Admin Dashboard</h1>

<div class="profiles core-tabs">

	<ul>
		<?php
		$link = $this->Permission->link('Merge Requests', array('controller' => 'merge_requests', 'model' => 'User'), array('title' => 'merge-requests'));
		echo $link ? $this->Html->tag('li', $link) : null;
		?>
	</ul>

	<div class="content-box clearfix">
		<?php if ($this->Permission->check(array('controller' => 'merge_requests'))): ?>
		<div id="merge-requests">
			<?php
			echo $this->requestAction('/merge_requests/index/model:User', array(
				'renderAs' => 'ajax',
				'return'
			));
			?>
		</div>
		<?php endif; ?>
	</div>
</div>
<h1><?php echo $campus['Campus']['name']; ?></h1>
<div class="campuses index content-box clearfix">
	<h2>Description</h2>
	<p><?php echo $campus['Campus']['description']; ?></p>
	<h2>Search</h2>
	<?php
	echo $this->element('search', array(
		 'model' => 'Campus',
		 'model_id' => $campus['Campus']['id'],
		 'term' => $campus['Campus']['name']
	));
	?>
	<div class="grid_10 alpha omega">
		<h3>Ministries</h3>
		<div class="subministries parent">
			<?php
			$url = Router::url(array(
				'controller' => 'ministries',
				'action' => 'index',
				'Campus' => $campus['Campus']['id']
			));
			echo $this->requestAction($url, array('renderAs' => 'ajax', 'return', 'bare' => false));
			?>
		</div>
	</div>
	<ul class="core-admin-tabs">
		<?php
		$link = $this->Permission->link('Add Ministry', array('controller' => 'ministries', 'action' => 'add', 'Campus' => $campus['Campus']['id']), array('rel' => 'modal-none'));
		echo !empty($link) ? $this->Html->tag('li', $link) : null;
		?>
	</ul>
</div>
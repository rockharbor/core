<h1><?php echo $campus['Campus']['name']; ?></h1>
<div class="campuses index content-box">
	<h2>Description</h2>
	<p><?php echo html_entity_decode($campus['Campus']['description']); ?></p>
	<h2>Search</h2>
	<?php
	echo $this->element('search', array(
		 'model' => 'Campus',
		 'model_id' => $campus['Campus']['id'],
		 'term' => $campus['Campus']['name']
	));
	?>
	<ul class="core-admin-tabs">
		<?php
		$link = $this->Permission->link('Add Ministry', array('controller' => 'ministries', 'action' => 'add', 'Campus' => $campus['Campus']['id']), array('rel' => 'modal-none'));
		echo !empty($link) ? $this->Html->tag('li', $link) : null;
		?>
	</ul>
</div>
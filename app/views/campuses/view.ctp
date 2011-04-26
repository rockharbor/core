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
</div>
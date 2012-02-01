<span class="breadcrumb"><?php
echo $this->Html->link($ministry['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $ministry['Campus']['id']), array('escape' => false));
if (!empty($ministry['ParentMinistry']['id'])) {
	echo ' > ';
	echo $this->Html->link($ministry['ParentMinistry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['ParentMinistry']['id']), array('escape' => false));
}
?></span>
<h1><?php echo $ministry['Ministry']['name'].$this->Formatting->flags('Ministry', $ministry); ?></h1>
<div class="content-box clearfix">
	<div class="grid_10 alpha omega">
		<h3>Description</h3>
		<p class="ministry-description">
			<?php 
			echo $ministry['Ministry']['description'];
			if (!empty($ministry['Image'])) {
				$path = 'm'.DS.$ministry['Image'][0]['dirname'].DS.$ministry['Image'][0]['basename'];
				echo $this->Media->embed($path, array('restrict' => 'image'));
			}
			?>
		</p>
	</div>
	
	<div class="grid_10 alpha omega">
		<h3>Get Involved!</h3>
	</div>
	<div class="grid_10 alpha omega">
		<div id="involvement" class="parent">
		<?php
			$url = Router::url(array(
				'controller' => 'involvements',
				'action' => 'index',
				'column',
				'Ministry' => $ministry['Ministry']['id']
			));
			$this->Js->buffer('CORE.register("involvement", "involvement", "'.$url.'")');
			echo $this->requestAction($url, array('renderAs' => 'ajax', 'return', 'bare' => false));
		?>
		</div>
	</div>
	<br />
	<?php if (empty($ministry['Ministry']['parent_id'])): ?>
	<div class="grid_10 alpha omega">
		<h3>Sub Ministries</h3>
		<div class="subministries parent">
			<?php
			$url = Router::url(array(
				'controller' => 'ministries',
				'action' => 'index',
				'Ministry' => $ministry['Ministry']['id']
			));
			echo $this->requestAction($url, array('renderAs' => 'ajax', 'return', 'bare' => false));
			?>
		</div>
	</div>
	<br />
	<?php endif; ?>
	<ul class="core-admin-tabs">
	<?php
	$link = $this->Permission->link('Edit', array('action' => 'edit', 'Ministry' => $ministry['Ministry']['id']));
	echo $link ? $this->Html->tag('li', $link) : null;
	$link = $this->Permission->link('Add Involvement Opportunity', array('controller' => 'involvements', 'action' => 'add', 'Ministry' => $ministry['Ministry']['id']));
	echo $link ? $this->Html->tag('li', $link) : null;
	if (empty($ministry['ParentMinistry']['id'])) {
		$link = $this->Permission->link('Add Subministry', array('controller' => 'ministries', 'action' => 'add', 'Ministry' => $ministry['Ministry']['id'], 'Campus' => $ministry['Campus']['id']), array('rel' => 'modal-none'));
		echo $link ? $this->Html->tag('li', $link) : null;
	}
	$link = $this->Permission->link('Delete', array('action' => 'delete', 'Ministry' => $ministry['Ministry']['id']), array('id' => 'delete_btn'));
	if ($link) {
		$this->Js->buffer('CORE.confirmation("delete_btn", "Are you sure you want to delete this ministry and all it\'s related content?", {update:"content"});');
		echo $this->Html->tag('li', $link);
	}
	?>
</ul>
</div>
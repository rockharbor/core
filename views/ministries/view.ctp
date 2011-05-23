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
		<h2>Description</h2>
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
	<?php if (!empty($ministry['ChildMinistry'])): ?>
	<br />
	<div class="grid_10 alpha omega">
		<h2>Sub Ministries</h2>
		<div class="subministries">
			<?php
			foreach ($ministry['ChildMinistry'] as $subministry):
			?>
			<div class="subministry">
				<?php
				echo $this->Html->link($subministry['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $subministry['id']), array('class' => 'subministry-title'));
				echo '<hr>';
				echo $this->Text->truncate($subministry['description'], 120, array('html' => true));
				?>
			</div>
			<?php 
			endforeach;
			if (count($ministry['ChildMinistry']) > 5) {
				$this->Html->script('misc/ministry', array('inline' => false, 'once' => true));
				$this->Js->buffer('CORE_ministry.setup();');
			}
			?>
		</div>
		<div class="pagination" style="display:none; text-align: center;">
			<?php
			echo $this->Html->link('Prev', 'javascript:;', array('class' => 'prev-button'));
			echo $this->Html->link('Next', 'javascript:;', array('class' => 'next-button'));
			?>
		</div>
	</div>
	<?php endif; ?>
	<br />
	<div class="grid_10 alpha omega">
		<h2>Get Involved!</h2>
	</div>
	<div class="grid_10 alpha omega">
		<div id="involvement">
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
	<ul class="core-admin-tabs">
	<?php
	$link = $this->Permission->link('Edit', array('action' => 'edit', 'Ministry' => $ministry['Ministry']['id']));
	if ($link) {
		echo $this->Html->tag('li', $link);
	}
	$link = $this->Permission->link('Add Involvement Opportunity', array('controller' => 'involvements', 'action' => 'add', 'Ministry' => $ministry['Ministry']['id']));
	if ($link) {
		echo $this->Html->tag('li', $link);
	}
	$link = $this->Permission->link('Delete', array('action' => 'delete', $ministry['Ministry']['id']), array('id' => 'delete_btn'));
	if ($link) {
		$this->Js->buffer('CORE.confirmation("delete_btn", "Are you sure you want to PERMANENTLY delete this ministry and all it\'s related content?", {update:"content"});');
		echo $this->Html->tag('li', $link);
	}
	?>
</ul>
</div>
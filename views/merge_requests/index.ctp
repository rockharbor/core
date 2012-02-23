<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Merge Requests</h1>
<div class="merge_requests">
	<?php foreach ($requests as $request): ?>
	<div class="clearfix">
		<div class="grid_4 alpha"><h3>Current Information</h3></div>
		<div class="grid_2">&nbsp;</div>
		<div class="grid_4 omega"><h3>New Information</h3></div>
		<div class="grid_4 alpha">
			<div class="box">
				<?php echo $this->element('merge'.DS.'user', array('user' => $request['OriginalModel'])); ?>
			</div>
		</div>
		<div class="grid_2">
			<?php
			$link = $this->Html->link('Merge', array('controller' => 'merge_requests', 'action' => 'merge', $request['MergeRequest']['id']), array('class' => 'flat-button green', 'id' => 'merge_btn_'.$request['MergeRequest']['id']));
			echo $this->Html->tag('div', $link);
			$this->Js->buffer('CORE.confirmation("merge_btn_'.$request['MergeRequest']['id'].'", "Are you sure you want to permanently merge these records?", {update:"content"})');
			$link = $this->Html->link('Delete', array('controller' => 'merge_requests', 'action' => 'delete', $request['MergeRequest']['id']), array('class' => 'flat-button red', 'id' => 'delete_btn_'.$request['MergeRequest']['id']));
			echo $this->Html->tag('div', $link);
			$this->Js->buffer('CORE.confirmation("delete_btn_'.$request['MergeRequest']['id'].'", "Are you sure you want to delete this merge request?", {update:"content"})');
			?>
		</div>
		<div class="grid_4 omega">
			<div class="box">
				<?php echo $this->element('merge'.DS.'user', array('user' => $request['NewModel'])); ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
	<?php echo $this->element('pagination'); ?>
</div>
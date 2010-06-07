<?php
$this->Paginator->options(array(
    'update' => '#comments',
    'evalScripts' => true
));
?>

<div class="comments">
	<h2>Comments</h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('comment_type_id');?></th>
			<th><?php echo $this->Paginator->sort('comment');?></th>
			<th><?php echo $this->Paginator->sort('created_by');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($comments as $comment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $comment['Comment']['id']; ?>&nbsp;</td>
		<td><?php echo $comment['CommentType']['name']; ?>&nbsp;</td>
		<td><?php echo $comment['Comment']['comment']; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->flags('User', $comment['Creator']).$comment['Creator']['username']; ?>&nbsp;</td>
		<td><?php echo $comment['Comment']['created']; ?>&nbsp;</td>
		<td><?php echo $comment['Comment']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php 
				echo $this->Js->link('Edit', array('controller' => 'comments', 'action' => 'edit', $comment['Comment']['id'], 'User' => $userId), array(
					'rel' => 'modal-comments'
				)); 
				echo $this->Html->link('Delete', array('controller' => 'comments', 'action' => 'delete', $comment['Comment']['id']), array(
					'id'=>'delete_comment_btn_'.$i
				)); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<?php

echo $this->Js->link('Add comment', 
	array(
		'controller' => 'comments', 'action' => 'add', 'User' => $userId),
		array(
			'class' => 'button',
			'rel' => 'modal-comments'
		)
);

$this->Html->scriptStart(array('inline' => true));

while ($i > 0) {
	echo 'CORE.confirmation(\'delete_comment_btn_'.$i.'\',\'Are you sure you want to delete this comment?\', {update:\'comments\'});';
	$i--;
}

echo $this->Html->scriptEnd();

?>
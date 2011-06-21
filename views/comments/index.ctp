<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Comments</h1>
<div class="comments">
	<?php
	$i = 0;
	foreach ($comments as $comment):
		$i++;
	?>
	<div class="comment clearfix">
		<div class="comment-image">
		<?php
		if (isset($activeUser['ImageIcon'])) {
			$path = 's'.DS.$activeUser['ImageIcon']['dirname'].DS.$activeUser['ImageIcon']['basename'];
			echo $this->Media->embed($path, array('restrict' => 'image'));
		}
		?>
		</div>
		<div class="comment-body">
			<div class="comment-title">
				<span class="float:left">
					<?php
					echo $this->Html->link($comment['Creator']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $comment['Creator']['id']));
					echo ' ('.$groups[$comment['Comment']['group_id']].') ';
					echo 'Commented on '.$this->Formatting->date($comment['Comment']['created']);
					?>
				</span>
				<span style="float:right">
					<?php
					echo $this->Js->link('Edit', array('action' => 'edit', 'Comment' => $comment['Comment']['id'], 'User' => $comment['Creator']['id']), array('id' => 'edit_comment_'.$i, 'class' => 'core-icon icon-edit', 'title' => 'Edit', 'update' => '#content'));
					echo $this->Html->link('Delete', array('action' => 'delete', 'Comment' => $comment['Comment']['id'], 'User' => $comment['Creator']['id']), array('id' => 'delete_comment_'.$i, 'class' => 'core-icon icon-delete', 'title' => 'Delete'));
					$this->Js->buffer('CORE.confirmation("delete_comment_'.$i.'", "Are you sure you want to delete this comment?", {updateHtml:"content"});')
					?>
				</span>
			</div>
			<div class="comment-comment">
				<?php echo $comment['Comment']['comment']; ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php echo $this->element('pagination'); ?>
<?php

echo $this->Js->link('Add comment', 
	array(
		'controller' => 'comments', 'action' => 'add', 'User' => $userId),
		array(
			'class' => 'button',
			'update' => '#content'
		)
);

?>
<?php
$this->Paginator->options(array(
    'update' => '#comments',
    'evalScripts' => true
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
		if (count($activeUser['Image']) > 0) {
			$path = 's'.DS.$activeUser['Image'][0]['dirname'].DS.$activeUser['Image'][0]['basename'];
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
					echo $this->Html->link('Delete', array('action' => 'delete', 'Comment' => $comment['Comment']['id'], 'User' => $comment['Creator']['id']), array('id' => 'delete_comment_'.$i, 'class' => 'core-icon icon-delete'));
					$this->Js->buffer('CORE.confirmation("delete_comment_'.$i.'", "You for sure?", {updateHtml:"content"});')
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
<table style="width:100%">
	<tfoot>
		<?php echo $this->element('pagination'); ?>
	</tfoot>
</table>
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
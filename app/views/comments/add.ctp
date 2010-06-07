<div class="comments">
<h2>Add Comment</h2>
<?php echo $this->Form->create('Comment', array('default' => false));?>
	<fieldset>
 		<legend>Add Comment</legend>
	<?php
		echo $this->Form->hidden('user_id', array(
			'value' => $userId
		));
		echo $this->Form->input('comment_type_id');
		echo $this->Form->input('comment');
		//echo $this->Form->input('created_by');
	?>
	</fieldset>
<?php
echo $this->Js->submit('Add', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
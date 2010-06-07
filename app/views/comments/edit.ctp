<div class="comments">
<h2>Edit Comment</h2>
<?php echo $this->Form->create('Comment', array('default' => false));?>
	<fieldset>
 		<legend>Edit Comment</legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('user_id');
		echo $this->Form->input('comment_type_id');
		echo $this->Form->input('comment');
	?>
	</fieldset>
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
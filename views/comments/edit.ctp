<div class="comments">
<h1>Edit Comment</h1>
<?php echo $this->Form->create('Comment', array('default' => false));?>
	<fieldset>
 		<legend>Edit Comment</legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('user_id');
		echo $this->Form->input('group_id', array(
			'label' => 'Comment Type'
		));
		echo $this->Form->input('comment', array(
			'type' => 'textarea'
		));
	?>
	</fieldset>
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
<div class="comments">
<h1>Add Comment</h1>
<?php echo $this->Form->create('Comment', array('default' => false));?>
	<fieldset>
 		<legend>Add Comment</legend>
	<?php
		echo $this->Form->hidden('user_id', array(
			'value' => $userId
		));
		echo $this->Form->hidden('group_id', array(
			'value' => $activeUser['Group']['id']
		));
		echo $this->Form->input('comment');
		echo $this->Form->hidden('created_by', array(
			'value' => $activeUser['User']['id']
		));
	?>
	</fieldset>
<?php
echo $this->Js->submit('Add', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
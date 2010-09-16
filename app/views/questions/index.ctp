<div class="questions">
	<h2>Questions</h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th>&nbsp;</th>
		<th>Question</th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($questions as $question):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php
		if (count($questions) > 1) {
			if ($question['Question']['order'] == 1) {
				echo $this->Js->link($this->Html->image('down.icon.png', array('alt' => 'Move Down')), array('action' => 'move', $question['Question']['id'], 'down'), array(
					'complete' => 'CORE.update("questions")',
					'escape' => false				
				));
			} elseif ($question['Question']['order'] == count($questions)) {
				echo $this->Js->link($this->Html->image('up.icon.png', array('alt' => 'Move Up')), array('action' => 'move', $question['Question']['id'], 'up'), array(
					'complete' => 'CORE.update("questions")',
					'escape' => false,
					'alt' => 'Move Up'
				));
			} else {
				echo $this->Js->link($this->Html->image('down.icon.png', array('alt' => 'Move Down')), array('action' => 'move', $question['Question']['id'], 'down'), array(
					'complete' => 'CORE.update("questions")',
					'escape' => false
				));		
				echo $this->Js->link($this->Html->image('up.icon.png', array('alt' => 'Move Up')), array('action' => 'move', $question['Question']['id'], 'up'), array(
					'complete' => 'CORE.update("questions")',
					'escape' => false
				));
			}
		}		
		?></td>
		<td><?php echo $question['Question']['description']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action' => 'edit', $question['Question']['id']), array('rel'=>'modal-questions')); ?>
			<?php echo $this->Html->link('Delete', array('action' => 'delete', $question['Question']['id']), array('id' => 'delete_btn_'.$i)); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>
<?php if (Core::read('involvements.question_limit') > $i): ?>
<p>
<?php echo $this->Html->link('Add Question', array('action' => 'add', 'Involvement' => $involvementId), array('rel'=>'modal-questions','class'=>'button')); ?>
</p>
<?php endif; ?>

<?php

while ($i > 0) {
	$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'","Are you sure you want to delete this question?", {update:"questions"});');
	$i--;
}

?>
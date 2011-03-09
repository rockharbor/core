<h1>Questions</h1>
<div class="questions">	
	<table cellpadding="0" cellspacing="0" class="datatable">
		<tbody>
		<?php
		$i = 0;
		foreach ($questions as $question):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' altrow';
			}
		?>
		<tr class="<?php echo $class;?>">
			<td>
				<div class="core-iconable" style="padding: 0 50px;">
					<div class="core-icon-container" style="left:0">
					<?php
					if (count($questions) > 1) {
						if ($question['Question']['order'] == 1) {
							$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-arrow2-s'));
							echo $this->Js->link($icon, array('action' => 'move', $question['Question']['id'], 'down'), array(
								'complete' => 'CORE.update("questions")',
								'escape' => false
							));
						} elseif ($question['Question']['order'] == count($questions)) {
							$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-arrow2-n'));
							echo $this->Js->link($icon, array('action' => 'move', $question['Question']['id'], 'up'), array(
								'complete' => 'CORE.update("questions")',
								'escape' => false,
								'alt' => 'Move Up'
							));
						} else {
							$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-arrow2-s'));
							echo $this->Js->link($icon, array('action' => 'move', $question['Question']['id'], 'down'), array(
								'complete' => 'CORE.update("questions")',
								'escape' => false
							));
							$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-arrow2-n'));
							echo $this->Js->link($icon, array('action' => 'move', $question['Question']['id'], 'up'), array(
								'complete' => 'CORE.update("questions")',
								'escape' => false
							));
						}
					}
					?>
					</div>

				<?php echo $question['Question']['description']; ?>&nbsp;

					<div class="core-icon-container">
					<?php
					$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-edit'));
					echo $this->Html->link($icon, array('action' => 'edit', $question['Question']['id']), array('rel'=>'modal-questions', 'escape' => false));
					$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-delete'));
					echo $this->Html->link($icon, array('action' => 'delete', $question['Question']['id']), array('id' => 'delete_btn_'.$i, 'escape' => false));
					?>
					</div>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
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
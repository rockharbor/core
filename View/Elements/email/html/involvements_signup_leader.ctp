<?php
$userList = $this->Text->toList(Set::extract('/Profile/name', $signedupUsers));
$payment = isset($amount) ?  ' and made a '.$this->Formatting->money($amount).' payment' : null;
?>
<?php echo $userList; ?> <?php echo $verb; ?> signed up<?php echo $payment; ?> for the <?php echo $involvement['InvolvementType']['name']; ?> <strong><?php echo $this->Html->link($involvement['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'], 'full_base' => true)); ?></strong>.
<?php
foreach ($signedupUsers as $signedupUser) {
	if (isset($signedupUser['answers'])) {
		echo $this->Html->tag('p', '<strong>'.$signedupUser['Profile']['name'].'</strong>');
		foreach ($signedupUser['answers'] as $answer) {
			echo '<p>';
			$question = Set::extract('/Question[id='.$answer['Answer']['question_id'].']/description', $involvement);
			echo $this->Html->tag('em', $question[0]);
			echo '<br />';
			echo $answer['Answer']['description'];
			echo '</p>';
		}
	}
}
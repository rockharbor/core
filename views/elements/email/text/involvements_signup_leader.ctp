<?php
$userList = $this->Text->toList(Set::extract('/Profile/name', $signedupUsers));
$payment = isset($amount) ?  ' and made a '.$this->Formatting->money($amount).' payment' : null;
?>
<?php echo $userList; ?> <?php echo $verb; ?> signed up<?php echo $payment; ?> for the <?php echo $involvement['InvolvementType']['name']; ?> <?php echo $involvement['Involvement']['name']; ?>.

<?php
foreach ($signedupUsers as $signedupUser) {
	if (isset($signedupUser['answers'])) {
		echo $signedupUser['Profile']['name'].PHP_EOL;
		echo '--------------------------------'.PHP_EOL;
		foreach ($signedupUser['answers'] as $answer) {
			$question = Set::extract('/Question[id='.$answer['Answer']['question_id'].']/description', $involvement);
			echo $question[0].PHP_EOL;
			echo $answer['Answer']['description'].PHP_EOL.PHP_EOL;
		}
	}
}
<?php
$userList = $this->Text->toList(Set::extract('/Profile/name', $signedupUsers));
$payment = isset($amount) ?  ' and made a '.$this->Formatting->money($amount).' payment' : null;
?>
<?php echo $userList; ?> <?php echo $verb; ?> signed up<?php echo $payment; ?> for the <?php echo $involvement['InvolvementType']['name']; ?> <?php echo $involvement['Involvement']['name']; ?>.

You are receiving this message because you are a Household Contact for one or more of these users.
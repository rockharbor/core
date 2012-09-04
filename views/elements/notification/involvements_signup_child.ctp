<?php
$userList = $this->Text->toList(Set::extract('/Profile/name', $signedupUsers));
$payment = isset($amount) ?  ' and made a '.$this->Formatting->money($amount).' payment' : null;
?>
<?php echo $userList; ?> <?php echo $verb; ?> signed up<?php echo $payment; ?> for the <?php echo $involvement['InvolvementType']['name']; ?> <strong><?php echo $this->Html->link($involvement['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])); ?></strong>.
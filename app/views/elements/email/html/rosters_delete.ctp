<h1>Leaving <?php echo $involvement['InvolvementType']['name']; ?></h1>
<p><?php 
if ($this->activeUser['User']['id'] == $user['User']['id']) {
	echo 'You have';
} else {
	echo $user['Profile']['name'].' has';
}?> left <?php echo $involvement['Involvement']['name']; ?>.</p>
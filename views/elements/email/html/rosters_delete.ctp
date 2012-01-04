<p>Leaving <?php echo $involvement['InvolvementType']['name']; ?></p>
<p><?php 
if ($activeUser['User']['id'] == $user['User']['id']) {
	echo 'You have';
} else {
	echo $user['Profile']['name'].' has';
}?> left <?php echo $involvement['Involvement']['name']; ?>.</p>
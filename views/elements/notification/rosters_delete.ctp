<?php 
if ($activeUser['User']['id'] == $user['User']['id']) {
	echo 'You have';
} else {
	echo $user['Profile']['name'].' has';
}?> been removed from <strong><?php echo $involvement['Involvement']['name']; ?></strong>.
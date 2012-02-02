<h1>Households</h1>
<p><?php
$usersNames = Set::extract('/Profile/name', $users);
echo $this->Text->toList($usersNames);
if (count($usersNames) > 1) {
	echo ' have';
} else {
	echo ' has';
}
echo ' been invited '.$contact['Profile']['name'].'\'s household.';
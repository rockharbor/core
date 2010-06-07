<?php
class HouseholdMember extends AppModel {
	var $name = 'HouseholdMember';
	
	var $belongsTo = array(
		'Household',
		'User'
	);
	
	var $actsAs = array(
		'Linkable.AdvancedLinkable'
	);

}

?>
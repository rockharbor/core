<?php
class PaymentType extends AppModel {
	var $name = 'PaymentType';

	var $hasMany = array(
		'Payment'
	);

}
?>
<?php

$phrases = array(
	'Ministry' => array(
		'Why don\'t you get involved in the :name ministry?',
		'I heard that the :name ministry needs volunteers.',
		'Check out :name! It\'s great!',
	),
	'Involvement' => array(
		'Get involved in :name today!',
		':name is the place to be.',
		'I\'d rather be involved in :name.',
	),
	'Generic' => array(
		'I lost 30lbs. on CORE 2.0',
		'Smile',
		'I\'m bored. Log in and get involved.',
		'To the cloud! ~whoosh!~',
		'Hi.',
		'Bienvenidos.',
		'Who am I? CORE, that\'s who.',
		':)',
		'CORE. Now with 78% more awesomeness.',
		'I like you.',
	)
);

if (!$model) {
	$rand = rand(0, count($phrases['Generic'])-1);
	$phrase = $phrases['Generic'][$rand];
} else {
	$rand = rand(0, count($phrases[$model])-1);
	$phrase = $phrases[$model][$rand];
	$phrase = String::insert($phrase, $result[$model]);
}

echo $this->Js->object(compact('phrase'));

?>
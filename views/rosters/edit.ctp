<h1>Signup</h1>

<div class="rosters">
<?php
/* 
check to see if this is a quick signup
	- involvement.take_payment = false
	- involvement.offer_childcare = false
	- no household members
*/

// get kids in their households
$children = Set::extract('/Profile[child=1]', $householdMembers);

echo $this->Form->create('Roster', array(
	'default' => false,
	'inputDefaults' => array()
));

echo $this->Form->hidden('Roster.id');
echo $this->Form->hidden('Roster.user_id');
echo $this->Form->hidden('Roster.involvement_id');

	// if this involvement offers childcare and this is the user we're signing up (i.e., the household contact)
	if ($involvement['Involvement']['offer_childcare'] && !empty($children)) {
?>
	<fieldset>
		<legend>Childcare Signups</legend>
		<p>This event offers childcare. Select children below to sign up for childcare.<?php
			if ($involvement['Involvement']['take_payment']) {
				echo ' Note: adding childcare to this '.$involvement['InvolvementType']['name'].' may increase your balance depending on the payment option you select.';
			}
		?></p>
<?php 
		$c = 0;
		foreach ($children as $child) {
			// see if they're already signed up
			if (in_array($child['Profile']['user_id'], $roster)) {
				echo $this->Form->input('Child.'.$c.'.user_id', array(
					'type' => 'checkbox',
					'value' => $child['Profile']['user_id'],
					'label' => $child['Profile']['name'],
					'checked' => 'checked',
					'disabled' => 'disabled'
				));
			} else {
				echo $this->Form->input('Child.'.$c.'.user_id', array(
					'type' => 'checkbox',
					'value' => $child['Profile']['user_id'],
					'label' => $child['Profile']['name']
				));
			}
			$c++;
		}
?>
	</fieldset>
<?php
	}

if (!empty($involvement['Question'])) {
	?>
	<fieldset>
		<legend>Questions</legend>
		<div class="core-tabs">
			<ul>
				<?php $q = 1; foreach ($involvement['Question'] as $question): ?>
				<li><?php echo $this->Html->link('Question '.$q, '#question'.$q); ?></li>
				<?php $q++; endforeach; ?>
			</ul>
	<?php
		$q = 0;
		$answers = Set::combine($this->data, '{n}.Answer.question_id', '{n}.Answer');
		foreach ($involvement['Question'] as $question) {
			echo '<div id="question'.($q+1).'">';
			echo $this->Html->tag('p', $question['description']);
			echo $this->Form->hidden('Answer.'.$q.'.id', array(
				'value' => $answers[$question['id']]['id']
			));
			echo $this->Form->input('Answer.'.$q.'.description', array(
				'label' => $answers[$question['id']]['description'],
				'type' => 'textarea'
			));
			$q++;
			echo '</div>';
		}
	?>
		</div>
	</fieldset>
	<?php 
	} 
	if ($fullAccess) {
	?>
	<fieldset>
 		<legend>Signup Options</legend>
	<?php		
		echo $this->Form->input('Roster.roster_status_id');
		
		if ($involvement['Involvement']['take_payment']) {
			echo $this->Form->input('Roster.payment_option_id', array(
				'empty' => 'No Payment Option',
				'after' => "<br/>Changing a user's payment option will affect their balance"
			));
		}
	?>
	</fieldset>
	
<?php 
	}
	echo $this->Js->submit('Save', $defaultSubmitOptions);
	echo $this->Form->end();
?>
</div>
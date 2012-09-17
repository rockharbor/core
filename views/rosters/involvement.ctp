<div id="roster-involvement">
	<h1>My Involvement</h1>
	<div class="content-box">
		<div style="margin-bottom: 10px;">
		<?php
		echo $this->Form->create(null, array(
			'class' => 'core-filter-form',
			'url' => $this->passedArgs,
		));
		echo $this->Form->input('household', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('previous', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('leading', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('inactive', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo $this->Form->input('private', array(
			'type' => 'checkbox',
			'class' => 'toggle',
			'div' => false
		));
		echo '<div>';
		foreach ($rosterStatuses as $status) {
			echo $this->Form->input('RosterStatus.'.$status, array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false
			));
		}
		echo '</div>';
		echo $this->Js->submit('Filter');
		echo $this->Form->end();
		?>
		</div>
		<?php
		foreach ($rosters as $involvement) { 
			foreach ($involvement['Roster'] as $roster) { 
				$roles = Set::extract('/Role/name', $roster);
				if (empty($roles) && in_array($involvement['Involvement']['id'], array_values($memberOf))) {
					$roles[] = 'Member';
				}
				if (in_array($involvement['Involvement']['id'], array_values($leaderOf)) && $roster['user_id'] == $userId) {
					array_unshift($roles, 'Leader');
				}
				$inv = '';
				if ($roster['user_id'] !== $userId) {
					$link = $this->Html->link($roster['User']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $roster['user_id']));
					$inv .= $link.$this->Formatting->flags('User', $roster).' is a ';
				}
				$inv .= $this->Text->toList($roles);
				$inv .= (count($roles) > 1 && !in_array('Member', $roles)) ? ' for ' : ' of ';
				$inv .= $this->Html->link($involvement['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id']));
				$inv_flags = array(
					'Involvement' => $involvement['Involvement'],
					'Date' => $involvement['Date'],
					'InvolvementType' => $involvement['InvolvementType']
				);
				$inv .= $this->Formatting->flags('Involvement', $inv_flags);
				if ($roster['balance'] > 0) {
					$due = $this->Formatting->money($roster['balance']);
					$link = $this->Html->link($due, array('controller' => 'payments', 'action' => 'add', 'Involvement' => $involvement['Involvement']['id'], $roster['id'], 'User' => $roster['user_id']), array('data-core-modal' => 'true', 'class' => 'balance'));
					$inv .= ' | '.$this->Html->tag('span', $link);
				}
				if (!empty($involvement['Involvement']['dates'])) {
					$inv .= ' | '.$this->Formatting->datetime($involvement['Involvement']['dates'][0]['Date']['start_date'].' '.$involvement['Involvement']['dates'][0]['Date']['start_time']);
				}
				if ($roster['roster_status_id'] > 1) {
					$inv .= ' | '.$rosterStatuses[$roster['roster_status_id']];
				}
				echo $this->Html->tag('p', $inv);
			}
		}
		echo $this->element('pagination'); 
		?>
	</div>
</div>
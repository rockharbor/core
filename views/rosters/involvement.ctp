<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<div id="roster-involvement">
	<h1>My Involvement</h1>
	<div class="content-box">
		<?php
		echo $this->Form->create(null, array(
			'class' => 'core-filter-form update-roster-involvement',
			'url' => $this->passedArgs,
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
		if ($private) {
			echo $this->Form->input('private', array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false
			));
		} else {
			echo $this->Form->hidden('private', array('value' => 0));
		}
		echo $this->Js->submit('Filter');
		echo $this->Form->end();
		?>
		<table>
			<tbody>
				<?php foreach ($rosters as $roster): ?>
				<tr>
					<td colspan="3"><?php
					$roles = Set::extract('/Roster/Role/name', $roster);
					if (empty($roles) && in_array($roster['Involvement']['id'], array_values($memberOf))) {
						$roles[] = 'Member';
					}
					if (in_array($roster['Involvement']['id'], array_values($leaderOf))) {
						array_unshift($roles, 'Leader');
					}
					$inv = $this->Text->toList($roles);
					$inv .= (count($roles) > 1 && !in_array('Member', $roles)) ? ' for ' : ' of ';
					$inv .= $this->Html->link($roster['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $roster['Involvement']['id']));
					$inv_flags = array(
						'Involvement' => $roster['Involvement'],
						'Date' => $roster['Date'],
						'InvolvementType' => $roster['InvolvementType']
					);
					$inv .= $this->Formatting->flags('Involvement', $inv_flags);
					if (!empty($roster['Roster']) && $roster['Roster'][0]['balance'] > 0) {
						$due = $this->Formatting->money($roster['Roster'][0]['balance']);
						$link = $this->Html->link($due, array('controller' => 'payments', 'action' => 'add', 'Involvement' => $roster['Involvement']['id'], $roster['Roster'][0]['id']), array('rel' => 'modal-involvement', 'class' => 'balance'));
						$inv .= ' | '.$this->Html->tag('span', $link);
					}
					if (!empty($roster['Involvement']['dates'])) {
						$inv .= ' | '.$this->Formatting->datetime($roster['Involvement']['dates'][0]['Date']['start_date'].' '.$roster['Involvement']['dates'][0]['Date']['start_time']);
					}
					echo $inv;
					?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo $this->element('pagination'); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php
$this->Paginator->options(array(
    'update' => '#involvement',
    'evalScripts' => true
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
		echo $this->Form->input('passed', array(
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
					$roles = Set::extract('/Role/name', $roster);
					if (empty($roles)) {
						$roles[] = 'Member';
					}
					if (in_array($roster['Involvement']['id'], array_values($leaderOf))) {
						array_unshift($roles, 'Leader');
					}
					$inv = $this->Text->toList($roles);
					$inv .= (count($roles) > 1) ? ' for ' : ' of ';
					$inv .= $this->Html->link($roster['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $roster['Involvement']['id']));
					$inv_flags = array(
						'Involvement' => $roster['Involvement'],
						'Date' => $roster['Date'],
						'InvolvementType' => $roster['InvolvementType']
					);
					$inv .= $this->Formatting->flags('Involvement', $inv_flags);
					if (!empty($roster['Roster']) && $roster['Roster'][0]['amount_due'] > 0) {
						$inv .= ' | '.$this->Html->tag('span', $roster['Roster'][0]['amount_due'], array('class' => 'balance'));
					}
					if (!empty($roster['Involvement']['dates'])) {
						$inv .= ' | '.$this->Formatting->datetime($roster['Involvement']['dates'][0]['start_date'].' '.$roster['Involvement']['dates'][0]['start_time']);
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
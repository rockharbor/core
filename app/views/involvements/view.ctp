<?php echo $this->Html->script('super_date'); ?>

<div class="involvements view">
<h2><?php  __('Involvement');?></h2>
<h3>Information</h3>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Ministry Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['ministry_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Involvement Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['InvolvementType']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Roster Limit'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['roster_limit']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Roster Visible'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['roster_visible']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Group</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['group_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Signup'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['signup']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Require Payment'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['take_payment']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Offer Childcare'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['offer_childcare']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $involvement['Involvement']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
	
<h3>When is it going down?</h3>
<?php 
$i = 0;
foreach ($involvement['Date'] as $date) {
	if (!$date['exemption']) {
		echo $this->Html->tag('div', '&nbsp;', array('escape' => false, 'id' => 'hr_'.$i));
		$this->Js->buffer('$("#hr_'.$i.'").text(CORE.makeHumanReadable({
			recurring: '.$date['recurring'].',
			type: \''.$date['recurrance_type'].'\',
			frequency: '.$date['frequency'].',
			day: '.$date['day'].',				
			weekday: '.$date['weekday'].',
			offset: '.$date['offset'].',
			allday: '.$date['all_day'].',
			permanent: '.$date['permanent'].',
			startDate: \''.$date['start_date'].'\',
			endDate: \''.$date['end_date'].'\',
			startTime: \''.$date['start_time'].'\',
			endTime: \''.$date['end_time'].'\'
		}))', array('onDomReady' => false));
		$i++;
	}
}
?>
	
<strong>Except these times</strong>
<?php 
$i = 0;
foreach ($involvement['Date'] as $date) {
	if ($date['exemption']) {
		echo $this->Html->tag('div', '&nbsp;', array('escape' => false, 'id' => 'hre_'.$i));
		$this->Js->buffer('$("#hre_'.$i.'").text(CORE.makeHumanReadable({
			recurring: '.$date['recurring'].',
			type: \''.$date['recurrance_type'].'\',
			frequency: '.$date['frequency'].',
			day: '.$date['day'].',				
			weekday: '.$date['weekday'].',
			offset: '.$date['offset'].',
			allday: '.$date['all_day'].',
			permanent: '.$date['permanent'].',
			startDate: \''.$date['start_date'].'\',
			endDate: \''.$date['end_date'].'\',
			startTime: \''.$date['start_time'].'\',
			endTime: \''.$date['end_time'].'\'
		}))', array('onDomReady' => false));
		$i++;
	}
}
?>

<div id="involvement"><?php
if ($involvement['Involvement']['roster_visible']) {
	$this->Js->buffer('CORE.register("involvement", "involvement", "'.Router::url(array('controller' => 'rosters', 'Involvement' => $involvement['Involvement']['id'])).'");');
	echo $this->requestAction(Router::url(array('controller' => 'rosters', 'Involvement' => $involvement['Involvement']['id'])), array('return','bare'=>true));
} 
?></div>

</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php 
		if ($involvement['Involvement']['signup']) {
			echo $this->Html->link('Sign up', array('controller' => 'rosters', 'action' => 'add', 'User' => $activeUser['User']['id'], 'Involvement' => $involvement['Involvement']['id']), array('rel' => 'modal-content')); 
		}		
		?> </li>
		<li><?php echo $this->Html->link('Edit Involvement', array('action' => 'edit', 'Involvement' => $involvement['Involvement']['id'])); ?> </li>
		<li><?php echo $this->Html->link('Invite Users', array('controller' => 'searches', 'action' => 'simple', 'User', 'notSignedUp', $involvement['Involvement']['id'], 'Invite To '.$involvement['InvolvementType']['name'] => 'invite'), array('rel' => 'modal-content')); ?> </li>
		<li><?php echo $this->Html->link('Invite Roster', array('controller' => 'searches', 'action' => 'simple', 'Involvement', 'notInvolvementAndIsLeading', $involvement['Involvement']['id'], $activeUser['User']['id'], 'Invite To '.$involvement['InvolvementType']['name'] => 'inviteRoster'), array('rel' => 'modal-content')); ?> </li>
	</ul>
</div>

<?php

echo $this->Html->scriptBlock('function invite(userid) {
	CORE.request("'.Router::url(array('controller' => 'involvements', 'action' => 'invite')).'/"+userid+"/'.$involvement['Involvement']['id'].'");
}

function inviteRoster(involvementid) {
	CORE.request("'.Router::url(array('controller' => 'involvements', 'action' => 'invite_roster')).'/"+involvementid+"/'.$involvement['Involvement']['id'].'");
}

');

?>
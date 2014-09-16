<h1><?php __('Emails');?></h1>
<div class="content-box">
	<?php
	echo $this->Form->create('Filter', array(
		'class' => 'core-filter-form',
		'url' => $this->here
	));
	?><h4>Shows only the first 500 messages</h4><?php
	echo $this->Form->input('show', array(
		'label' => 'Show messages',
		'options' => array(
			'to' => 'to me',
			'from' => 'from me',
			'both' => 'both'
		)
	));
	echo $this->Form->input('hide_system', array(
		'label' => 'Hide system emails',
		'type' => 'checkbox'
	));
	echo $this->Js->submit('Filter', $defaultSubmitOptions);
	echo $this->Form->end();

	?>
	<table class="datatable">
		<thead>
			<tr>
				<?php if ($this->data['Filter']['show'] !== 'from'): ?>
				<th><?php echo $this->Paginator->sort('From', 'FromUser.Profile.name'); ?></th>
				<?php endif; ?>
				<?php if ($this->data['Filter']['show'] !== 'to'): ?>
				<th><?php echo $this->Paginator->sort('To', 'ToUser.Profile.name'); ?></th>
				<?php endif; ?>
				<th><?php echo $this->Paginator->sort('subject'); ?></th>
				<th><?php echo $this->Paginator->sort('Created', 'created'); ?></th>
				<th><?php echo $this->Paginator->sort('Sent', 'modified'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($emails as $email): ?>
			<tr>
				<?php if ($this->data['Filter']['show'] !== 'from'): ?>
				<td><?php echo $email['SysEmail']['from']; ?>&nbsp;</td>
				<?php endif; ?>
				<?php if ($this->data['Filter']['show'] !== 'to'): ?>
				<td><?php echo $email['ToUser']['Profile']['name']; ?>&nbsp;</td>
				<?php endif; ?>
				<td><?php echo $this->Html->link($email['SysEmail']['subject'], array('controller' => 'sys_emails', 'action' => 'view', $email['SysEmail']['id'], 'User' => $this->passedArgs['User']), array('data-core-modal' => '{"update":"false"}')); ?>&nbsp;</td>
				<td><?php echo $this->Formatting->datetime($email['SysEmail']['created']); ?>&nbsp;</td>
				<td><?php echo $this->Formatting->datetime($email['SysEmail']['modified']); ?>&nbsp;</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->element('pagination'); ?>
</div>
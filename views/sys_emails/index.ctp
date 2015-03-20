<h1><?php __('Emails');?></h1>
<div class="content-box">
	<?php
	echo $this->Form->create('Filter', array(
		'class' => 'core-filter-form',
		'url' => $this->here
	));
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
		'type' => 'checkbox',
		'div' => array(
			'class' => 'input checkbox clearfix'
		)
	));
	?> <div id="advancedOptions" style="display: none;"> <?php
	echo $this->Form->input('show_all', array(
		'label' => 'Show all emails<br/>Warning: This will take a LONG time and you might need to refresh the page',
		'type' => 'checkbox',
		'div' => array(
			'class' => 'input checkbox clearfix'
		)
	));
	?> </div> <?php
	echo $this->Js->submit('Filter', $defaultSubmitOptions);
	echo $this->Form->end();
	$this->Js->buffer('$("#toggleAdvanced").click(function(){$("#advancedOptions").toggle();});');
	?>
	<button id="toggleAdvanced">Advanced options</button>
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
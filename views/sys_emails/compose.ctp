<?php
$this->MultiSelect->create();
if (!isset($this->passedArgs['mstoken'])) {
	$this->passedArgs['mstoken'] = $this->MultiSelect->token;
}
?>
<h1>Compose Email</h1>
<div class="email">
	<?php
	if (empty($fromUser['Profile']['primary_email'])) {
		$link = $this->Html->link('your account', array('controller' => 'profiles', 'action' => 'edit', 'User' => $fromUser['User']['id']));
		echo $this->Html->tag('div', "You cannot send emails without a valid email address associated with $link.", array('class' => 'notice', 'escape' => false));
	}
	?>
	<fieldset>
	<?php
		$toEmails = array();
		foreach ($toUsers as $user) {
			$toEmails[] = $user['Profile']['name'].' <'.$user['Profile']['primary_email'].'>';
		}

		$fromEmail = $fromUser['Profile']['name'].' <'.$fromUser['Profile']['primary_email'].'>';

		echo $this->Html->tag(
			'div',
			$this->Form->label('from').$this->Html->tag('div', $fromEmail, array('escape' => true)),
			array(
				'id' => 'SysEmailFrom',
				'class' => 'input',
				'escape' => false
			)
		);
		$append = count($toUserIds) > count($toUsers) ? ' (20 shown)' : null;
		echo $this->Html->tag(
			'div',
			$this->Form->label('to', 'To '.count($toUserIds).' Users'.$append).$this->Html->tag('div', implode(', ',$toEmails), array('escape' => true)),
			array(
				'id' => 'SysEmailTo',
				'class' => 'input',
				'escape' => false
			)
		);
		?>
	</fieldset>
	<?php
	if ($showAttachments && $this->Permission->check(array('controller' => 'sys_email_documents', 'action' => 'index'))):
		$url = Router::url(array(
			'controller' => 'sys_email_documents',
			'action' => 'index',
			'SysEmail' => $this->MultiSelect->token,
			'mstoken' => $this->MultiSelect->token
		));
	?>
	<div id="document_attachments" data-core-update-url="<?php echo $url; ?>">
		<?php
		/*
		we're going to use the attachment uploader here. after the email has been sent, the attachments will
		be removed from the server
		*/
		if ($showAttachments) {
			echo $this->requestAction($url, array(
				'return',
				'bare' => false,
				'renderAs' => 'ajax'
			));
		}
		?>
	</div>
	<?php endif; ?>
		<?php
		if (strpos($this->here, 'mstoken') === false) {
			$this->here = rtrim($this->here, '/').'/mstoken:'.$this->MultiSelect->token;
		}
		if (strpos($this->here, 'mspersist') === false) {
			$this->here = rtrim($this->here, '/').'/mspersist:1';
		}
		echo $this->Form->create('SysEmail', array(
			'default' => false,
			'url' => $this->here
		));
		?>
	<fieldset>
	<?php
		echo $this->Form->hidden('SysEmail.to', array(
			'value' => $this->data['SysEmail']['to']
		));
		echo $this->Form->input('SysEmail.subject', array(
			'between' => Core::read('sys_emails.subject_prefix'),
			'style' => 'width:300px'
		));
		echo $this->Form->input('SysEmail.body', array(
			'type' => 'textarea',
			'label' => 'Body',
			'escape' => false
		));
		if (empty($this->data['SysEmail']['email_users'])) {
			$this->data['SysEmail']['email_users'] = 'users';
		}
		if ($showPreferences) {
			echo $this->Html->tag(
				'div',
				$this->Form->input('email_users', array(
					'type' => 'radio',
					'options' => array(
						'users' => 'Selected Users',
						'household_contact' => 'Household Contacts',
						'both' => 'Both'
					),
					'value' => $this->data['SysEmail']['email_users']
				)),
				array(
					'id' => 'SysEmailEmailUsers',
					'class' => 'input',
					'escape' => false
				)
			);
		}
	?>
	</fieldset>
	<?php if ($showPreferences): ?>
	<fieldset>
		<legend>Preferences</legend>
		<?php
		echo $this->Form->input('SysEmail.include_greeting', array(
			'type' => 'checkbox',
			'label' => 'Include automatic greeting by the user\'s first name'
		));
		echo $this->Form->input('SysEmail.include_signoff', array(
			'type' => 'checkbox',
			'label' => 'Include '.Core::read('general.site_name').' signoff'
		));
		?>
	</fieldset>
	<?php endif; ?>
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
$defaultSubmitOptions['url'] = $this->here;
if (empty($fromUser['Profile']['primary_email'])) {
	$defaultSubmitOptions['class'] = 'disabled';
}
echo $this->Js->submit('Send', $defaultSubmitOptions);
echo $this->Form->end();

$this->Js->buffer('CORE.wysiwyg("SysEmailBody");');
?>
</div>
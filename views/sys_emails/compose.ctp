<?php
$this->MultiSelect->create();
if (!isset($this->passedArgs['mstoken'])) {
	$this->passedArgs['mstoken'] = $this->MultiSelect->token;
}
?>
<h1>Compose Email</h1>
<div class="email">
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
		echo $this->Html->tag(
			'div', 
			$this->Form->label('to', 'To '.count($toEmails).' Users').$this->Html->tag('div', $this->Text->truncate(implode(', ',$toEmails), 500), array('escape' => true)),
			array(
				'id' => 'SysEmailTo',
				'class' => 'input',
				'escape' => false
			)
		);
		?>
	</fieldset>
	<?php if ($showAttachments && $this->Permission->check(array('controller' => 'sys_email_documents', 'action' => 'index'))): ?>
	<div id="document_attachments">
		<?php
		/*
		we're going to use the attachment uploader here. after the email has been sent, the attachments will
		be removed from the server
		*/
		if ($showAttachments) {
			$url = '';
			$this->Js->buffer('CORE.register(
				"DocumentAttachments",
				"document_attachments",
				"/sys_email_documents/index/SysEmail:'.$this->MultiSelect->token.'/mstoken:'.$this->MultiSelect->token.'"
			);');

			echo $this->requestAction('/sys_email_documents/index', array(
				'return',
				'bare' => false,
				'renderAs' => 'ajax',
				'named' => array(
					'SysEmail' => $this->MultiSelect->token,
					'mstoken' => $this->MultiSelect->token
				)
			));
		}
		?>
	</div>
	<?php endif; ?>
		<?php 
		if (strpos($this->here, 'mstoken') === false) {
			$this->here = rtrim($this->here, '/').'/mstoken:'.$this->MultiSelect->token;
		}
		echo $this->Form->create('SysEmail', array(
			'default' => false,
			'url' => $this->here
		));
		?>
	<fieldset>
	<?php
		echo $this->Form->input('SysEmail.subject', array(
			'between' => Core::read('notifications.email_subject_prefix').' ',
			'style' => 'width:300px'
		));
		echo $this->Form->input('SysEmail.body');
		if (empty($this->data['SysEmail']['email_users'])) {
			$this->data['SysEmail']['email_users'] = 'users';
		}
		echo $this->Html->tag(
			'div',
			$this->Form->label('Email Users') .
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
	?>
	</fieldset>
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
$defaultSubmitOptions['url'] = $this->here;
echo $this->Js->submit('Send', $defaultSubmitOptions);
echo $this->Form->end();

echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("SysEmailBody");');
?>
</div>
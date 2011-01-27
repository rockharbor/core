<?php
$this->MultiSelect->create();
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
			$this->Form->label('to').$this->Html->tag('div', implode(', ',$toEmails), array('escape' => true)),
			array(
				'id' => 'SysEmailTo',
				'class' => 'input',
				'escape' => false
			)
		);
		?>
	</fieldset>
	<?php if ($showAttachments): ?>
	<div id="document_attachments">
		<?php
		/*
		we're going to use the attachment uploader here. after the email has been sent, the attachments will
		be removed from the server
		*/
		if ($showAttachments) {
			$this->Js->buffer('CORE.register("DocumentAttachments", "document_attachments", "'.Router::url(array(
				'controller' => 'sys_email_documents',
				'SysEmail' => $this->MultiSelect->token
			)).'");');

			$this->Js->buffer('CORE.update("DocumentAttachments");');
		}
		?>
	</div>
	<?php endif; ?>
		<?php echo $this->Form->create('SysEmail', array(
			'default' => false,
			'url' => $this->passedArgs
		));?>
	<fieldset>
	<?php
		echo $this->Form->input('SysEmail.subject', array(
			'between' => Core::read('notifications.email_subject_prefix').' ',
			'style' => 'width:300px'
		));
		if ($bodyElement && empty($this->data)) {
			$this->data['SysEmail']['body'] = $this->element($bodyElement, $this->viewVars);
		}
		echo $this->Form->input('SysEmail.body');
	?>
	</fieldset>
<?php
//$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';

echo $this->Js->submit('Send', $defaultSubmitOptions);
echo $this->Form->end();

echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("SysEmailBody");');

?>
</div>
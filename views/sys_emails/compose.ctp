<div class="email">
<h2>Compose Email</h2>

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
	<div id="email_attachments">
		<?php
		/*
		we're going to use the attachment uploader here. after the email has been sent, the attachments will
		be removed from the server
		*/
		if ($showAttachments) {
			$this->Js->buffer('CORE.register("DocumentAttachments", "email_attachments", "'.Router::url(array(
				'controller' => 'sys_email_documents',
				'SysEmail' => $cacheuid
			)).'");');
			
			$this->Js->buffer('CORE.update("DocumentAttachments");');
		}
		?>
	</div>
		<?php echo $this->Form->create('SysEmail', array(
			'default' => false,
			'url' => array(
				$cacheuid
			)
		));?>
	<fieldset>
	<?php
		echo $this->Form->input('SysEmail.subject', array(
			'between' => Configure::read('CORE.settings.email_subject_prefix').' ',
			'style' => 'width:300px'
		));
		$val = (empty($this->data['SysEmail']['body']) && $bodyElement) ? $this->element($bodyElement, $this->viewVars) : $this->data['SysEmail']['body'];
		echo $this->Form->input('SysEmail.body', array(
			'value' => $val
		));
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
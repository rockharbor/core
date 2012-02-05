<?php
$this->Html->script('login', array('inline' => false));
$this->Js->buffer('CORE.initLogin()');
?>
<div class="grid_6 prefix_3 suffix_3">
	<div id="logo">
		<?php echo $this->Html->image('logo.png', array('alt' => Core::read('general.site_name_tagless'))); ?>
	</div>
	<?php
	echo $this->Form->create('User');
	?>
	<div id="login-form" class="clearfix">
		<?php
		echo $this->Form->input('username', array(
			'size' => 31,
			'div' => 'input text showhide'
		));
		echo $this->Form->input('password', array(
			'size' => 31,
			'div' => 'input password showhide'
		));
		?>
	</div>
	<div id="login-info">
		<?php
		echo $this->Form->input('remember_me', array(
			'type' => 'checkbox',
			'label' => 'Forget me not'
		));
		echo ' | ';
		$new = $this->Html->tag('span', '[new] ', array('style' => 'color:red'));
		echo $new.$this->Html->link('Watch me', array('controller' => 'pages', 'action' => 'display', 'welcome'), array('rel' => 'modal-none', 'escape' => false));
		echo ' | ';
		echo $this->Html->link('Trouble logging in?', array('action' => 'forgot_password'), array('rel' => 'modal-none'));
		echo ' | ';
		echo $this->Html->link('Sign Up', array('action' => 'register'), array('rel' => 'modal-none'));
		echo $this->Form->end('Whoosh!');
		?>
	</div>
</div>
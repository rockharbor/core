<?php
$this->Html->script('login', array('inline' => false));
//$this->Js->buffer('CORE.initLogin()');
?>
<div class="grid_8 prefix_2 suffix_2">
	<p style="font-family: arial, sans-serif; font-size: 24px; color: #000; line-height: 125%">CORE has been retired as of 12/31/2015. Please visit myRH at <a href="https://rockharbor.ccbchurch.com/">https://rockharbor.ccbchurch.com/</a> to get involved at RH.</p>
</div>
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
			'size' => 26,
			'div' => 'input text showhide'
		));
		echo $this->Form->input('password', array(
			'size' => 26,
			'div' => 'input password showhide'
		));
		?>
	</div>
	<div id="login-info">
		<?php
		// Remember me is broken, so remove the input
		/*echo $this->Form->input('remember_me', array(
			'type' => 'checkbox',
			'label' => 'Forget me not'
		));
		echo ' | ';*/
		echo $this->Html->link('Trouble logging in?', array('action' => 'forgot_password'), array('data-core-modal' => '{"update":false, "width": 500}'));
		echo ' | ';
		if (Core::read('notifications.support_email')) {
			echo $this->Html->link('Support', 'mailto:'.Core::read('notifications.support_email'));
			echo ' | ';
		}
		echo $this->Html->link('Sign Up', array('action' => 'register'), array('data-core-modal' => '{"update":false}'));
		echo $this->Form->end('Login');
		?>
	</div>
</div>

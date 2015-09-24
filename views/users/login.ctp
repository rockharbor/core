<?php
$this->Html->script('login', array('inline' => false));
//$this->Js->buffer('CORE.initLogin()');
$this->Js->buffer('
$("#myrh-dialog").dialog({
	autoOpen: false,
	modal: true,
	width: 500
});
$("#myrh-dialog-toggle").click(function(e) {
	e.preventDefault();
	$("#myrh-dialog").dialog("open");
});
$("#myrh-notice-close").click(function(e) {
	e.preventDefault();
	$("#myrh-notice").toggle("fold");
});
');
?>
<div id="myrh-notice" class="grid_12">
	<style scoped type="text/css">
		#myrh-notice {
			border-left: 4px solid #ffba00;
			font-size: 18px;
			font-family: arial, sans-serif;
			box-shadow: 0 0 3px 2px rgba(0, 0, 0, 0.1);
			color: #000;
		}
		#myrh-notice a {
			text-decoration: underline;
		}
		#myrh-notice div {
			margin: 10px 0 10px 0;
		}
		#myrh-notice div p {
			margin: 0;
		}
	</style>
	<div class="grid_10 prefix_1 alpha"><p>We're upgrading CORE to myRH on 9/15/2015. <a href="#" id="myrh-dialog-toggle">Click here for more information.</a></p></div>
	<div class="grid_1 omega"><a href="#" id="myrh-notice-close"><span class="core-icon icon-delete">close</span></a></div>
</div>
<div id="myrh-dialog">
	<style scoped type="text/css">
		#myrh-dialog {
			font-size: 14px;
		}
	</style>
	<p><strong>ROCK</strong>HARBOR has upgraded its Church Management Software. As of 9/15/2015, you'll be able to visit <a href="http://my.rockharbor.org/">my.rockharbor.org</a> to manage your family, sign up for events, and interact with the groups and teams you're a part of.</p>
	<p>If you had a CORE account your information has been automatically migrated to the new software. Once the migration is complete, you can click the <a href="https://rockharbor.ccbchurch.com/w_sign_up.php">Sign Up</a> link on myRH to activate your account. If you run into any trouble, just email <a href="mailto:myrh@rockharbor.org">myrh@rockharbor.org</a> to get help.</p>
	<p>If you already have an event sign-up link for CORE, you can still register for current events using CORE. Any events with registration starting after 9/15 will be using myRH. Talk to the leader of your event or group if you're unsure where to sign up, or email <a href="mailto:myrh@rockharbor.org">myrh@rockharbor.org</a> for more info.</p>
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

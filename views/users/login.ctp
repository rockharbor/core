<h2>Login</h2>

<?php
echo $this->Form->create('User');
?>
<fieldset>
<?php	
	echo $this->Form->input('username');
	echo $this->Form->input('password');	
?>
</fieldset>
<?php
echo $this->Html->link('Forgot password', array('action' => 'forgot_password'), array('rel' => 'modal-none'));
echo $this->Form->end('Login');

?>
<style type="text/css">
.error-message { display:none; }
</style>
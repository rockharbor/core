<div class="alerts">
<h2><?php echo $alert['Alert']['name']; ?></h2>
<p><?php echo $alert['Alert']['description']; ?></p>
<p><?php echo $this->Js->link('Okay, thanks', array('action' => 'read', $alert['Alert']['id']), array(
	'complete' => 'redirect("'.$referer.'"); CORE.closeModals()'
)); ?></p>
</div>
<h1>Merge Requests</h1>
<div class="merge_requests">
	<p>These users have requested that their account information be merged. Their new account is disabled until you take action here.</p>
	<p>
		<strong>Merge:</strong> You should merge accounts if you know that the accounts are one and the same. Merging an account will 
		update the current information with the new information and remove the new user from the system, leaving their current user 
		and all involvement in tact.
	</p>
	<p>
		<strong>Ignore:</strong> You should ignore the request if the new user is not the same as the current user. The new user will be
		activated automatically. Both users remain in the system.
	</p>
	<p>
		<strong>Deny:</strong> Use this for completely invalid requests. The new information will be discarded entirely and the current
		user is left untouched.
	</p>
	<?php foreach ($requests as $request): ?>
	<div class="clearfix">
		<div class="grid_4 alpha"><h3>Current Information</h3></div>
		<div class="grid_2">&nbsp;</div>
		<div class="grid_4 omega"><h3>New Information</h3></div>
		<div class="grid_4 alpha">
			<div class="box">
				<?php echo $this->element('merge'.DS.'user', array('user' => $request['OriginalModel'])); ?>
			</div>
		</div>
		<div class="grid_2">
			<?php
			$link = $this->Html->link('Merge', array('controller' => 'merge_requests', 'action' => 'merge', $request['MergeRequest']['id']), array('class' => 'flat-button green', 'id' => 'merge_btn_'.$request['MergeRequest']['id']));
			echo $this->Html->tag('div', $link);
			$this->Js->buffer('CORE.confirmation("merge_btn_'.$request['MergeRequest']['id'].'", "Are you sure you want to update the current user with the new information?", {update:true})');
			$link = $this->Html->link('Ignore', array('controller' => 'merge_requests', 'action' => 'delete', $request['MergeRequest']['id'], 1), array('class' => 'flat-button', 'id' => 'ignore_btn_'.$request['MergeRequest']['id']));
			echo $this->Html->tag('div', $link);
			$this->Js->buffer('CORE.confirmation("ignore_btn_'.$request['MergeRequest']['id'].'", "Are you sure you want to ignore this merge request? The new user will be activated and the request will be deleted. The original user will remain untouched.", {update:true})');
			$link = $this->Html->link('Deny', array('controller' => 'merge_requests', 'action' => 'delete', $request['MergeRequest']['id']), array('class' => 'flat-button red', 'id' => 'delete_btn_'.$request['MergeRequest']['id']));
			echo $this->Html->tag('div', $link);
			$this->Js->buffer('CORE.confirmation("delete_btn_'.$request['MergeRequest']['id'].'", "Are you sure you want to deny this merge request? The new user will be deleted!", {update:true})');
			?>
		</div>
		<div class="grid_4 omega">
			<div class="box">
				<?php echo $this->element('merge'.DS.'user', array('user' => $request['NewModel'])); ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
	<?php echo $this->element('pagination'); ?>
</div>
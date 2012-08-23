<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<div class="users index">
	<h2><?php __('Users');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('username');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('last_logged_in');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $user['User']['id']; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->flags('User', $user).$user['User']['username']; ?>&nbsp;</td>
		<td><?php echo $user['User']['active']; ?>&nbsp;</td>
		<td><?php echo $user['User']['last_logged_in']; ?>&nbsp;</td>
		<td><?php echo $user['User']['created']; ?>&nbsp;</td>
		<td><?php echo $user['User']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Js->link('Forgot password!', array(
				'action' => 'forgot_password', 
				'User'=>$user['User']['id']
			), 
			array(
				'complete' => 'CORE.update($("users"));'
			)); ?>
			<?php echo $this->Html->link('Delete', array('controller' => 'users', 'action' => 'delete', $user['User']['id']), array(
					'id'=>'delete_user_btn_'.$i
				)); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Js->link('New User', array('action' => 'add'), array(
			'data-core-modal' => 'true'
		)); ?></li>
	</ul>
</div>

<?php
$this->Html->scriptStart(array('inline' => true));

while ($i > 0) {
	echo 'CORE.confirmation(\'delete_user_btn_'.$i.'\',\'Are you sure you want to delete this user?\', {update:true});';
	$i--;
}

echo $this->Html->scriptEnd();


<div class="profiles index">
	<h2><?php __('Profiles');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('first_name');?></th>
			<th><?php echo $this->Paginator->sort('last_name');?></th>
			<th><?php echo $this->Paginator->sort('gender');?></th>
			<th><?php echo $this->Paginator->sort('birth_date');?></th>
			<th><?php echo $this->Paginator->sort('adult');?></th>
			<th><?php echo $this->Paginator->sort('classification_id');?></th>
			<th><?php echo $this->Paginator->sort('marital_status');?></th>
			<th><?php echo $this->Paginator->sort('job_category_id');?></th>
			<th><?php echo $this->Paginator->sort('occupation');?></th>
			<th><?php echo $this->Paginator->sort('accepted_christ');?></th>
			<th><?php echo $this->Paginator->sort('accepted_christ_year');?></th>
			<th><?php echo $this->Paginator->sort('baptism_date');?></th>
			<th><?php echo $this->Paginator->sort('allergies');?></th>
			<th><?php echo $this->Paginator->sort('special_needs');?></th>
			<th><?php echo $this->Paginator->sort('special_alert');?></th>
			<th><?php echo $this->Paginator->sort('cell_phone');?></th>
			<th><?php echo $this->Paginator->sort('home_phone');?></th>
			<th><?php echo $this->Paginator->sort('work_phone');?></th>
			<th><?php echo $this->Paginator->sort('primary_email');?></th>
			<th><?php echo $this->Paginator->sort('alternate_email_1');?></th>
			<th><?php echo $this->Paginator->sort('alternate_email_2');?></th>
			<th><?php echo $this->Paginator->sort('cpr_certified_date');?></th>
			<th><?php echo $this->Paginator->sort('baby_dedication_date');?></th>
			<th><?php echo $this->Paginator->sort('qualified_leader');?></th>
			<th><?php echo $this->Paginator->sort('background_check_complete');?></th>
			<th><?php echo $this->Paginator->sort('background_check_by');?></th>
			<th><?php echo $this->Paginator->sort('background_check_date');?></th>
			<th><?php echo $this->Paginator->sort('grade');?></th>
			<th><?php echo $this->Paginator->sort('graduation_year');?></th>
			<th><?php echo $this->Paginator->sort('User.last_logged_in');?></th>
			<th><?php echo $this->Paginator->sort('created_by');?></th>
			<th><?php echo $this->Paginator->sort('created_by_type');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($profiles as $profile):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $profile['Profile']['id']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['user_id']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['first_name']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['last_name']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['gender']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['birth_date']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['adult']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['classification_id']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['marital_status']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['job_category_id']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['occupation']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['accepted_christ']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['accepted_christ_year']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['baptism_date']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['allergies']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['special_needs']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['special_alert']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['cell_phone']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['home_phone']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['work_phone']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['primary_email']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['alternate_email_1']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['alternate_email_2']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['cpr_certified_date']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['baby_dedication_date']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['qualified_leader']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['background_check_complete']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['background_check_by']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['background_check_date']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['grade']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['graduation_year']; ?>&nbsp;</td>
		<td><?php echo $profile['User']['last_logged_in']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['created_by']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['created_by_type']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['created']; ?>&nbsp;</td>
		<td><?php echo $profile['Profile']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('controller' => 'users', 'action' => 'edit_profile', 'User' => $profile['User']['id'])); ?>
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
		<li><?php echo $this->Html->link('Register', array('controller' => 'users', 'action' => 'add')); ?></li>
	</ul>
</div>
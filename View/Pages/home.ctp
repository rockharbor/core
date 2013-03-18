<h2><?php echo Core::read('general.site_name').' '.Core::read('version'); ?> / <?php
echo $this->Html->link(Core::read('general.church_name'), Core::read('general.church_site_url'), array('escape' => false));
?></h2>
Admin-y sort of things
<ul>
	<li><?php echo $this->Html->link('Alerts', array('controller' => 'alerts')); ?></li>
	<li><?php echo $this->Html->link('App Settings', array('controller' => 'app_settings')); ?></li>
	<li><?php echo $this->Html->link('Merge Requests', array('controller' => 'merge_requests', 'action' => 'index', 'model' => 'User')); ?></li>
	<li><?php echo $this->Html->link('Payment Types', array('controller' => 'payment_types')); ?></li>
	<li><?php echo $this->Html->link('Permission Groups', array('controller' => 'groups')); ?></li>
	<li><?php echo $this->Html->link('Profiles', array('controller' => 'profiles')); ?></li>
	<li><?php echo $this->Html->link('Users', array('controller' => 'users')); ?></li>
</ul>
<br />
Church happenings
<ul>
	<li><?php echo $this->Html->link('Calendar', array('controller' => 'dates', 'action' => 'calendar')); ?></li>
	<li><?php echo $this->Html->link('Campuses', array('controller' => 'campuses')); ?></li>
	<li><?php echo $this->Html->link('Involvement Opportunities', array('controller' => 'involvements')); ?></li>
	<li><?php echo $this->Html->link('Involvement Opportunity Types', array('controller' => 'involvement_types')); ?></li>
	<li><?php echo $this->Html->link('Ministries', array('controller' => 'ministries')); ?></li>
</ul>
<br />
Other things
<ul>
	<li><?php echo $this->Html->link('Error logs', array('controller' => 'errors')); ?></li>
	<li><?php echo $this->Html->link('Logs', array('controller' => 'logs')); ?></li>
	<li><?php echo $this->Html->link('People Search', array('controller' => 'searches', 'action' => 'user')); ?></li>
</ul>
<br />
Lists, etc.
<ul>
	<li><?php echo $this->Html->link('Job Categories', array('controller' => 'job_categories')); ?></li>
	<li><?php echo $this->Html->link('Regions', array('controller' => 'regions')); ?></li>
	<li><?php echo $this->Html->link('Schools', array('controller' => 'schools')); ?></li>
</ul>

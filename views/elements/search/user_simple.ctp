<div class="clearfix">
	<h1>Search Users</h1>
	<div class="grid_5 alpha">
	<?php
		echo $this->Form->input('User.username');
		echo $this->Form->input('Profile.primary_email');
	?>
	</div>
	<div class="grid_5 omega">
		<?php
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
		?>
	</div>
</div>
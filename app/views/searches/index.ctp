<h2>Results</h2>
<h3>Users&nbsp;<small><?php echo $this->Html->link('[Advanced Search]', array('action'=>'user'));?></small></h3>
<ul>
<?php
// just print out for now
foreach ($users as $user):
?>
<li><?php echo $this->Formatting->flags('User', $user).$this->Text->highlight($user['User']['username'], $query);
	echo '&nbsp;&nbsp;&nbsp;';
	echo $this->Html->link(
		$this->Text->highlight($user['Profile']['name'], $query), 
		array('controller' => 'users', 'action' => 'edit_profile', 'User' => $user['User']['id']),
		array('escape' => false)
	);
?></li>

<?php	
endforeach;
?>
</ul>

<h3>Ministries&nbsp;<small><?php echo $this->Html->link('[Advanced Search]', array('action'=>'ministry'));?></small></h3>
<ul>
<?php
// just print out for now
foreach ($ministries as $ministry):
?>
<li><?php 
	echo $this->Formatting->flags('Ministry', $ministry);
	echo $this->Html->link(
		$this->Text->highlight($ministry['Ministry']['name'], $query), 
		array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id']),
		array('escape' => false)
	);
	echo '<br />';
	echo $this->Text->highlight($this->Text->excerpt($ministry['Ministry']['description'], $query, 20), $query);
?></li>

<?php	
endforeach;
?>
</ul>

<h3>Involvement Opportunities&nbsp;<small><?php echo $this->Html->link('[Advanced Search]', array('action'=>'involvement'));?></small></h3>
<ul>
<?php
// just print out for now
foreach ($involvements as $involvement):

?>
<li><?php echo $this->Formatting->flags('Involvement', $involvement);
	echo $this->Html->link(
		$this->Text->highlight($involvement['Involvement']['name'], $query), 
		array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id']),
		array('escape' => false)
	);
	echo '<br />';
	echo $this->Text->highlight($this->Text->excerpt($involvement['Involvement']['description'], $query, 20), $query);
?></li>

<?php	
endforeach;
?>
</ul>
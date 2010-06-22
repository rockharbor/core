<h1>Results</h1>
<?php if (!empty($users)) { ?>
<h2>Users&nbsp;<small><?php echo $this->Html->link('[Advanced Search]', array('action'=>'user'));?></small></h2>
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
<?php }

if (!empty($ministries)) { ?>
<h2>Ministries&nbsp;<small><?php echo $this->Html->link('[Advanced Search]', array('action'=>'ministry'));?></small></h2>
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
<?php }

if (!empty($involvements)) { ?>
<h2>Involvement Opportunities&nbsp;<small><?php echo $this->Html->link('[Advanced Search]', array('action'=>'involvement'));?></small></h2>
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
<?php }

if (empty($users) && empty($ministries) && empty($involvements)) { ?>

<p>Whoops, no results for <?php echo $this->Text->highlight($query, $query); ?>. This ain't <span style="color: blue;">G</span><span style="color: red;">o</span><span style="color: yellow;">o</span><span style="color: blue;">g</span><span style="color: green;">l</span><span style="color: red;">e</span>&reg;, so try again with something less specific. Remember, the search algorithm right now isn't very strong. Wait for beta ;)</p>

<?php } ?>
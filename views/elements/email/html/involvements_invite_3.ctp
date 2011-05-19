<h1>Invitation to <?php echo $involvement['Involvement']['name']; ?></h1>
<p>Visit <?php echo $this->Html->link(null, array(
	'controller' => 'involvements',
	'action' => 'view',
	'Involvement' => $involvement['Involvement']['id']
)); ?> to view this <?php echo $involvement['InvolvementType']['name']; ?> by <?php echo $notifier['Profile']['name']; ?>.</p>
<p>You have been invited to join the <?php echo $involvement['InvolvementType']['name']; ?> <strong><?php echo $involvement['Involvement']['name']; ?></strong> by <?php echo $notifier['Profile']['name']; ?>.</p>
<p>Visit <?php echo $this->Html->link($involvement['Involvement']['name'], array(
	'controller' => 'involvements',
	'action' => 'view',
	'Involvement' => $involvement['Involvement']['id']
), true); ?> to view this <?php echo $involvement['InvolvementType']['name']; ?>.</p>
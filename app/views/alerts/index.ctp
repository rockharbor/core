<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Alerts</h1>
<div class="alerts index">
	<?php
	foreach ($alerts as $alert):
	?>
	<div class="box core-iconable">
		<?php
		echo $this->Html->tag('strong', $this->Html->link($alert['Alert']['name'], array('action' => 'view', $alert['Alert']['id']), array('rel' => 'modal-none')));
		echo '<br />';
		echo $this->Html->tag('p', $this->Text->truncate($alert['Alert']['description'], 500));
		?>
		<div class="core-icon-container">
			<?php
			$icon = $this->element('icon', array('icon' => 'edit'));
			echo $this->Html->link($icon, array('action' => 'edit', $alert['Alert']['id']), array('rel' => 'modal-content', 'escape' => false, 'class' => 'no-hover'));
			$icon = $this->element('icon', array('icon' => 'delete'));
			echo $this->Html->link($icon, array('action' => 'delete', $alert['Alert']['id']), array('id' => 'delete-btn-'.$alert['Alert']['id'], 'escape' => false, 'class' => 'no-hover'));
			$this->Js->buffer('CORE.confirmation("delete-btn-'.$alert['Alert']['id'].'", "Are you sure you want to delete this alert?", {update:"content"})');
			?>
		</div>
	</div>
	<p>Visible to: <span class="red"><?php echo $alert['Group']['name']; ?> and above</span> <span class="deemphasized">(read by <?php echo $alert['Alert']['read_by_users']; ?> users)</span><br />
		Date expires: <span class="deemphasized"><?php echo empty($alert['Alert']['expires']) ? 'Never' : $this->Formatting->date($alert['Alert']['expires']); ?></span>
	</p>
<?php endforeach; ?>
<?php echo $this->element('pagination'); ?>
</div>
<ul class="core-admin-tabs">
	<li><?php echo $this->Permission->link('New Alert', array('action' => 'add'), array('rel' => 'modal-content')); ?></li>
</ul>
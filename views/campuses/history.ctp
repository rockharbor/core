<h2>Campus Revision</h2>
<?php
if (!empty($revision)) {
// save revision id
$version_created = $revision['Revision']['version_created'];

// remove stuff we don't want in the diff
unset($revision['Revision']['version_id']);
unset($revision['Revision']['version_created']);

$changes = array_diff_assoc($revision['Revision'], $campus['Campus']);
$changes = Set::filter($changes);
?>
<p>The following changes have been requested for <strong><?php echo $campus['Campus']['name']; ?></strong> on <strong><?php echo $this->Formatting->date($version_created); ?></strong></p>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
	<?php foreach ($changes as $changeField => $changeValue): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Original <?php echo $changeField; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php 
			$varName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $changeField)));
			if (isset(${$varName})) {
				echo ${$varName}[$campus['Campus'][$changeField]];
			} else {
				echo $campus['Campus'][$changeField];
			}
			
			?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Revised <?php echo $changeField; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php 
			$varName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $changeField)));
			if (isset(${$varName})) {
				echo ${$varName}[$changeValue];
			} else {
				echo $changeValue;
			}
			
			?>
			&nbsp;
		</dd>
	<?php endforeach; ?>
	</dl>

<?php 
echo $this->Js->link('Accept', 
	array(
		'action' => 'revise',
		'Campus' => $campus['Campus']['id'],
		1
	),
	array(
		'class' => 'button',
		'complete' => 'CORE.closeModals();'
	)
);
echo $this->Js->link('Deny', 
	array(
		'action' => 'revise',
		'Campus' => $campus['Campus']['id'],
		0
	),
	array(
		'class' => 'button',
		'complete' => 'CORE.closeModals();'
	)
);

} else {
?>

<p>There are no revisions to <?php echo $campus['Campus']['name']; ?>.</p>

<?php
}
?>
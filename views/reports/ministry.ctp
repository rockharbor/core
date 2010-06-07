<?php

$activeMinistries = Set::extract('/Ministry[active=1]', $ministries);
$inactiveMinistries = Set::extract('/Ministry[active=0]', $ministries);
$privateMinistries = Set::extract('/Group/id', $ministries);

?>

<div class="reports">
<h2>Ministry Report</h2>

<?php

echo $this->Form->create('Ministry', array(
	'default' => false,
	'inputDefaults' => array(
		'empty' => true
	),
	'url' => array(
		'controller' => 'reports',
		'action' => 'ministry'
	)
));

?>
<fieldset>
	<legend>Filter results</legend>
<?php
	echo $this->Form->input('campus_id');
	echo $this->Form->input('id', array(
		'label' => 'Ministry',
		'options' => $ministryList
	));
	// `both` needs to be last for it to be the default, since `0` is considered empty as well
	echo $this->Form->input('active', array(
		'type' => 'radio',
		'options' => array(			
			'0' => 'Inactive',
			'1' => 'Active',
			'' => 'Both'
		),
		'value' => empty($this->data) ? '' : $this->data['Ministry']['active']
	));
?>
</fieldset>
<?php
echo $this->Js->submit('Get Report', $defaultSubmitOptions);
echo $this->Form->end();
?>
	<h3>Ministries</h3>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<?php if ($this->data['Ministry']['active'] || $this->data['Ministry']['active'] === ''): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Active</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo count($activeMinistries); ?> / <?php echo count($ministries); ?> 
			&nbsp;
		</dd>
		<?php endif; ?>
		<?php if (!$this->data['Ministry']['active'] || $this->data['Ministry']['active'] === ''): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Inactive</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo count($inactiveMinistries); ?> / <?php echo count($ministries); ?> 
			&nbsp;
		</dd>
		<?php endif; ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Private</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo count($privateMinistries); ?> / <?php echo count($ministries); ?> 
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Leaders aka Managers</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo count(Set::extract('/Leader/id', $ministries)); ?>
			&nbsp;
		</dd>		
	</dl>
	
	<h3>Involvement Opportunities</h3>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<?php foreach ($involvementTypes as $involvementTypeId => $involvementTypeName): ?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo Inflector::pluralize($involvementTypeName); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo count(Set::extract('/Involvement[involvement_type_id='.$involvementTypeId.']', $ministries)); ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>>&nbsp;&nbsp;Leaders</dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo count(Set::extract('/Involvement[involvement_type_id='.$involvementTypeId.']/Leader/id', $ministries)); ?>
				&nbsp;
			</dd>
		<?php endforeach; ?>
	</dl>
	
	
	<p>This could go on...</p>
</div>
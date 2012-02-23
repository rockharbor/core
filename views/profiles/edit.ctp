<span class="breadcrumb editing"><?php
$icon = $this->element('icon', array('icon' => 'delete'));
echo $this->Html->link($icon, array('action' => 'view', 'User' => $this->data['Profile']['user_id']), array('escape' => false, 'class' => 'no-hover'));
?>Editing<?php echo $this->Html->image('../assets/images/edit-flag-right.png'); ?></span>
<h1><?php echo $this->data['Profile']['first_name'].' '.$this->data['Profile']['last_name']; ?></h1>

<div class="profiles core-tabs">
<?php
echo $this->Form->create(array(
	'url' => $this->passedArgs,
	'inputDefaults' => array(
		'empty' => true
	)
));
?>
	<ul>
		<li><a href="#personal-information">Personal</a></li>
		<li><a href="#contact-information">Contact</a></li>
		<?php if ($this->data['Profile']['child']): ?>
		<li><a href="#child-information">Child Needs</a></li>
		<?php endif; ?>
		<li><a href="#school-information">School</a></li>
		<li><?php echo $this->Html->link('Subscriptions', array('controller' => 'publications', 'action' => 'subscriptions', 'User' => $this->data['Profile']['user_id']), array('title' => 'subscriptions')); ?></li>
	</ul>

	<div class="content-box clearfix">
		<div id="personal-information">
			<?php echo $this->element('non_migratable', array('data' => $this->data['Profile']['non_migratable'])); ?>
			<fieldset class="grid_5 alpha">
				<legend>Personal Info</legend>
				<?php
				echo $this->Form->input('id');
				echo $this->Form->input('first_name');
				echo $this->Form->input('last_name');
				echo $this->Form->input('gender', array(
					'type' => 'select',
					'options' => $this->SelectOptions->genders
				));
				echo $this->Form->input('marital_status', array(
					'type' => 'select',
					'options' => $this->SelectOptions->maritalStatuses
				));
				echo $this->Form->input('birth_date', array(
					'minYear' => 1900,
					'maxYear' => date('Y')
				));
				echo $this->Form->hidden('adult', array('value' => $this->data['Profile']['adult']));
				?>
			</fieldset>
			<fieldset class="grid_5 omega">
				<legend>About Me</legend>
				<?php
				echo $this->Form->input('job_category_id');
				echo $this->Form->input('occupation');
				echo $this->Form->input('campus_id', array(
					'label' => 'What campus do you most often attend?'
				));
				echo $this->Form->input('classification_id');
				echo $this->Form->input('accepted_christ');
				echo $this->Form->input('accepted_christ_year', array(
					'type' => 'select',
					'options' => $this->SelectOptions->generateOptions('year', array(
						'min' => 1900,
						'max' => date('Y')
					))
				));
				echo $this->Form->input('baptism_date', array(
					'type' => 'date',
					'minYear' => 1900,
					'maxYear' => date('Y')
				));
				?>
			</fieldset>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<div id="contact-information">
			<?php echo $this->element('register'.DS.'phone_email'); ?>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<?php if ($this->data['Profile']['child']): ?>
		<div id="child-information">
			<?php echo $this->element('non_migratable', array('data' => $this->data['Profile']['non_migratable'])); ?>
			<?php echo $this->element('register'.DS.'child_info'); ?>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<?php endif; ?>
		<div id="school-information">
			<?php echo $this->element('register'.DS.'school'); ?>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<div id="subscriptions">
			<?php
			echo $this->requestAction('/publications/subscriptions', array(
				'named' => array(
					'User' => $this->data['Profile']['user_id']
				),
				'return',
				'renderAs' => 'ajax'
			));
			?>
		</div>
	</div>
<?php
echo $this->Form->end();
?>
</div>
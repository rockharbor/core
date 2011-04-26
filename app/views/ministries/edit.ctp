<span class="breadcrumb editing"><?php
$icon = $this->element('icon', array('icon' => 'delete'));
echo $this->Html->link($icon, array('action' => 'view', 'Ministry' => $this->passedArgs['Ministry']), array('escape' => false, 'class' => 'no-hover'));
?>Editing<?php echo $this->Html->image('../assets/images/edit-flag-right.png'); ?></span>
<h1><?php echo $this->data['Ministry']['name']; ?></h1>

<div class="ministries core-tabs">
<?php
if (!empty($revision)) {
	$changes = array_diff_assoc($revision, $this->data['Ministry']);
}

if ($revision && !empty($changes)): ?>
<div id="change" class="message change">
	There is a pending change for this ministry
	<?php
	echo $this->Permission->link('History', array('action' => 'history','Ministry' => $this->data['Ministry']['id']),array('rel' => 'modal-content', 'class' => 'button')
	);
	?>
</div>
<?php endif; ?>
	<ul>
		<li><a href="#ministry-information">Details</a></li>
		<li><a href="#ministry-leaders">Leaders</a></li>
		<li><a href="#ministry-roles">Roles</a></li>
		<li><a href="#ministry-attachments">Attachments</a></li>
	</ul>

	<div class="content-box clearfix">

		<div id="ministry-information">
			<?php
			echo $this->Form->create(array(
				'url' => $this->passedArgs,
				'inputDefaults' => array(
					'empty' => true
				)
			));
			?>
			<fieldset>
				<legend><?php printf(__('Edit %s', true), __('Ministry', true)); ?></legend>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('name');
				echo $this->Form->input('description', array(
					'type' => 'textarea',
					'cols' => 100,
					'value' => html_entity_decode($this->data['Ministry']['description'])
				));
				echo $this->Form->input('parent_id', array(
					'options' => $ministries,
					'escape' => false,
					'empty' => true,
					'label' => 'Parent Ministry'
				));
				echo $this->Form->input('campus_id');
				echo $this->Form->input('private');
				echo $this->Form->input('active');
			?>
			</fieldset>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div id="ministry-leaders">
			<?php
			$this->Js->buffer('CORE.register("leaders", "ministry-leaders", "/ministry_leaders/index/Ministry:'.$this->data['Ministry']['id'].'");');
			echo $this->requestAction('/ministry_leaders/index', array(
				'renderAs' => 'ajax',
				'bare' => false,
				'return',
				'named' => array(
					'Ministry' => $this->data['Ministry']['id'],
					'User' => $activeUser['User']['id']
				),
				'data' => null,
				'form' => array('data' => null)
			));
			?>
		</div>
		<div id="ministry-roles">
			<?php
				echo $this->Js->buffer('CORE.register("roles", "ministry-roles", "/roles/index/Ministry:'.$this->data['Ministry']['id'].'/User:'.$activeUser['User']['id'].'");');
				echo $this->requestAction('/roles/index', array(
					'renderAs' => 'ajax',
					'return',
					'named' => array(
						'Ministry' => $this->data['Ministry']['id'],
						'User' => $activeUser['User']['id']
					),
					'data' => null,
					'form' => array('data' => null)
				));
				?>
		</div>
		<div id="ministry-attachments">
			<div id="ministry-images">
				<?php
				echo $this->Js->buffer('CORE.register("ImageAttachments", "ministry-images", "/ministry_images/index/Ministry:'.$this->data['Ministry']['id'].'");');
				echo $this->requestAction('/ministry_images/index', array(
					'renderAs' => 'ajax',
					'bare' => false,
					'return',
					'named' => array(
						'Ministry' => $this->data['Ministry']['id'],
						'User' => $activeUser['User']['id']
					),
					'data' => null,
					'form' => array('data' => null)
				));
				?>
			</div>
		</div>
	</div>

<?php
echo $this->Html->script('jquery.plugins/jquery.wysiwyg', array('once' => true));
echo $this->Html->css('jquery.wysiwyg', array('once' => true));
$this->Js->buffer('CORE.wysiwyg("MinistryDescription");');
?>
</div>
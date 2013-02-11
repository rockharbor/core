<span class="breadcrumb editing"><?php
$icon = $this->element('icon', array('icon' => 'delete'));
echo $this->Html->link($icon, array('action' => 'view', 'Campus' => $this->passedArgs['Campus']), array('escape' => false, 'class' => 'no-hover'));
?>Editing<?php echo $this->Html->image('../assets/images/edit-flag-right.png'); ?></span>
<h1><?php echo $this->data['Campus']['name']; ?></h1>
<div class="campuses campuses core-tabs">
<?php
if (!empty($revision)) {
	$changes = array_diff_assoc($revision, $this->data['Campus']);
}

if ($revision && !empty($changes)): ?>
<div id="change" class="message change">
	There is a pending change for this campus
	<?php
	echo $this->Permission->link('History', array('action' => 'history','Campus' => $this->data['Campus']['id']),array('data-core-modal' => 'true', 'class' => 'button')
	);
	?>
</div>
<?php endif; ?>
	<ul>
		<li><a href="#campus-information">Details</a></li>
		<li><a href="#campus-leaders">Leaders</a></li>
	</ul>
	<div class="content-box clearfix">
		<div id="campus-information">
			<?php
			echo $this->Form->create('Campus', array(
				'default' => false,
				'url' => $this->here
			));
			?>
			<fieldset>
				<legend><?php printf(__('Edit %s', true), __('Campus', true)); ?></legend>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('name');
				echo $this->Form->input('description', array(
					'type' => 'textarea',
					'escape' => false
				));
				echo $this->Form->input('active');
			?>
			</fieldset>
			<?php
			echo $this->Js->submit(__('Submit', true), $defaultSubmitOptions);
			echo $this->Form->end();
			?>
		</div>
		<?php
		$url = Router::url(array(
			'controller' => 'campus_leaders',
			'action' => 'index',
			'Campus' => $this->data['Campus']['id'],
			'User' => $activeUser['User']['id']
		));
		?>
		<div id="campus-leaders" data-core-update-url="<?php echo $url; ?>">
			<?php
			echo $this->requestAction($url, array(
				'renderAs' => 'ajax',
				'bare' => false,
				'return',
				'data' => null,
				'form' => array('data' => null)
			));
			?>
		</div>
	</div>
</div>
<?php
$this->Js->buffer('CORE.wysiwyg("CampusDescription");');
?>
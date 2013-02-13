<h1>Ministries</h1>
<div class="content-box clearfix">
	<div class="grid_10 alpha omega">
		<div class="grid_5 alpha">
			<?php
			echo $this->Form->create(null, array(
				'class' => 'core-filter-form',
				'url' => $this->here,
			));
			echo $this->Form->input('inactive', array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false
			));
			if ($private) {
				echo $this->Form->input('private', array(
					'type' => 'checkbox',
					'class' => 'toggle',
					'div' => false
				));
			}
			echo $this->Js->submit('Filter');
			echo $this->Form->end();
			?>
		</div>
	</div>
	<div class="grid_10 alpha omega">
		<?php
		foreach ($ministries as $ministry):
		?>
		<div class="subministry">
			<?php
			echo $this->Html->link($ministry['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id']), array('class' => 'subministry-title')).$this->Formatting->flags('Ministry', $ministry);
			echo '<hr>';
			echo $this->Html->tag('p', $this->Text->truncate($ministry['Ministry']['description'], 200, array('html' => true)));
			?>
		</div>
		<?php
		endforeach;
		?>
	</div>
	<div><?php echo $this->element('pagination'); ?></div>
</div>
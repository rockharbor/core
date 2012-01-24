<?php
if (!empty($this->data['Search']['query'])) {
	echo $this->Html->tag('span', 'Results for "'.$this->data['Search']['query'].'"', array('class' => 'breadcrumb'));
}
?>
<h1>Search</h1>
<div class="content-box clearfix">
<?php if (!empty($users) || !empty($ministries) || !empty($involvements)): ?>
	<div>
	<?php
		echo $this->Form->create('Search', array(
			'class' => 'core-filter-form update-content',
			'url' => $this->passedArgs,
			'id' => 'SearchFilterForm'
		));

		echo $this->Html->tag('div',
			$this->Form->input('Search.Campus.id', array(
				'multiple' => 'checkbox',
				'label' => false,
				'options' => $campuses,
				'div' => false,
				'id' => 'SearchFilterCampusId'
			)),
			array('class' => 'toggle')
		);
		if ($inactive) {
			echo $this->Form->input('active', array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false,
				'id' => 'SearchFilterActive'
			));
			echo $this->Form->input('previous', array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false,
				'id' => 'SearchFilterPrevious'
			));
		}
		if ($private) {
			echo $this->Form->input('private', array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false,
				'id' => 'SearchFilterPrivate'
			));
		}
		echo $this->Form->hidden('query', array(
			'id' => 'SearchFilterQuery'
		));
		echo $this->Js->submit('Filter');
		echo $this->Form->end();
	?>
	</div>
<?php endif; ?>
<?php if (!empty($users)) { ?>
	<h3>Users</h3>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = '';
		if (($i+3)%3 == 0) {
			$class = ' alpha';
		} elseif (($i+4)%3 == 0) {
			$class = ' omega';
		}
		if ($class == ' alpha') {
			echo '<div class="clearfix" style="padding-bottom: 10px;">';
		}
		$i++;
	?>
	<div class="grid_third<?php echo $class; ?>">
		<div class="offset-background">
		<?php
			echo $this->Formatting->flags('User', $user).$this->Html->link($user['Profile']['name'], array(
				'controller' => 'profiles',
				'action' => 'view',
				'User' => $user['User']['id']
			));
			echo '<hr>';
			echo $this->Formatting->email($user['Profile']['primary_email'], $user['User']['id']);
			echo '<br />';
			echo $this->Formatting->phone($user['Profile']['cell_phone']);
			if ($class == ' omega') {
				echo '</div>';
			}
		?>
		</div>
	</div>

	<?php
	endforeach;
	if ($class != ' omega') {
		echo '</div>';
	}
	?>
	<?php echo $this->Permission->link('Advanced Search', array('action' => 'user'), array('class' => 'button')); ?>
	<hr />
<?php }

if (!empty($ministries)) { ?>
	<h3>Ministries</h3>
	<div class="subministries clearfix">
<?php
foreach ($ministries as $ministry):
?>
	<div class="subministry">
<?php 
	echo $this->Html->link($ministry['Ministry']['name'].$this->Formatting->flags('Ministry', $ministry),
		array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id']),
		array('escape' => false, 'class' => 'subministry-title')
	);
	echo '<hr />';
	echo $this->Html->tag('p', $this->Text->excerpt($ministry['Ministry']['description'], $this->data['Search']['query'], 100).'&nbsp;');
?></div>

<?php	
endforeach;
?>
	</div>
	<?php echo $this->Permission->link('Advanced Search', array('action' => 'ministry'), array('class' => 'button')); ?>
	<hr />
<?php }

if (!empty($involvements)) { ?>
	<h3>Involvement Opportunities</h3>
	<div class="grid_10 alpha omega">
	<?php
	$i = 0;
	foreach ($involvements as $involvement):
		$class = ($i % 2 == 0) ? 'alpha' : 'omega';
		$i++;
		echo $this->element('involvement_column', compact('involvement', 'class'));
	endforeach;
	?>
	</div>
	<?php echo $this->Permission->link('Advanced Search', array('action' => 'involvement'), array('class' => 'button')); ?>
	<hr />
<?php }
	
if (empty($users) && empty($ministries) && empty($involvements)) { ?>

<p>Whoops, no results. This ain't <span style="color: blue;">G</span><span style="color: red;">o</span><span style="color: yellow;">o</span><span style="color: blue;">g</span><span style="color: green;">l</span><span style="color: red;">e</span>&trade;, so try again with something less specific.</p>

<?php
echo $this->element('search');
} ?>
</div>
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
			echo $this->Form->input('passed', array(
				'type' => 'checkbox',
				'class' => 'toggle',
				'div' => false,
				'id' => 'SearchFilterPassed',
				'label' => 'Include Past'
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
<div class="hr">
	<div class="legend">People</div>
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
</div>
<?php }

if (!empty($ministries)) { ?>
<div class="hr">
	<div class="legend">Ministries</div>
<?php
foreach ($ministries as $ministry):
?>
	<p>
<?php 
	echo $this->Formatting->flags('Ministry', $ministry);
	$link = $this->Html->link(html_entity_decode($ministry['Ministry']['name']),
		array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id']),
		array('escape' => false)
	);
	echo $this->Html->tag('strong', $link);
	echo ':&nbsp;';
	echo $this->Text->excerpt(html_entity_decode($ministry['Ministry']['description']), $this->data['Search']['query'], 100);
?></p>

<?php	
endforeach;
?>
</div>
<div class="grid_10 alpha omega" style="text-align: right">
	<?php echo $this->Permission->link('Advanced Search', array('action' => 'ministry')); ?>
</div>
<?php }

if (!empty($involvements)) { ?>
<div class="hr">
	<div class="legend">Involvement Opportunities</div>
	<div class="grid_10 alpha omega">
	<?php
	$i = 0;
	foreach ($involvements as $involvement):
		$class = ($i % 2 == 0) ? 'alpha' : 'omega';
		$i++;
		echo $this->element('involvement-column', compact('involvement', 'class'));
endforeach;
?>
	</div>
</div>
<div class="grid_10 alpha omega" style="text-align: right">
	<?php echo $this->Permission->link('Advanced Search', array('action' => 'involvement')); ?>
</div>
<?php }
	
if (empty($users) && empty($ministries) && empty($involvements)) { ?>

<p>Whoops, no results. This ain't <span style="color: blue;">G</span><span style="color: red;">o</span><span style="color: yellow;">o</span><span style="color: blue;">g</span><span style="color: green;">l</span><span style="color: red;">e</span>&trade;, so try again with something less specific.</p>

<?php
echo $this->element('search');
} ?>
</div>
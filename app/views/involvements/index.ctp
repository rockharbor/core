<?php

$this->Js->buffer('CORE.fallbackRegister("involvement");');
$this->Paginator->options(array(
    'updateable' => 'involvement'
));
?>
<h1>Involvement Opportunities</h1>
<div class="content-box clearfix">
	<div class="grid_10 alpha omega">
		<div class="grid_5 alpha">
			<?php
			echo $this->Form->create(null, array(
				'class' => 'core-filter-form update-involvement',
				'url' => $this->passedArgs,
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
			} else {
				echo $this->Form->hidden('private', array('value' => 0));
			}
			echo $this->Js->submit('Filter');
			echo $this->Form->end();
			?>
		</div>
		<div class="grid_5 omega" style="text-align: right">
			<?php
			switch ($viewStyle) {
				case 'column':
					echo $this->Js->link('List View', array('action' => 'index', 'list', 'Ministry' => $this->passedArgs['Ministry']), array('update' => '#involvement'));
				break;
				case 'list':
					echo $this->Js->link('Column View', array('action' => 'index', 'column', 'Ministry' => $this->passedArgs['Ministry']), array('update' => '#involvement'));
				break;
			}
			?>
		</div>
	</div>
	<div class="grid_10 alpha omega">
		<?php
		$i = 0;
		if ($viewStyle == 'list') {
			echo '<table><tbody>';
		}
		foreach ($involvements as $involvement):
			$class = ($i % 2 == 0) ? 'alpha' : 'omega';
			$i++;
			switch ($viewStyle) {
			case 'column':
				echo $this->element('involvement-column', array('involvement' => $involvement, 'class' => $class));
			break;
			case 'list':
			default:
			echo '<tr>';
			$breadcrumb = '';
			$breadcrumb .= $this->Html->link($involvement['Ministry']['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $involvement['Ministry']['Campus']['id']));
			$breadcrumb .= ' > ';
			if (!empty($involvement['Ministry']['ParentMinistry'])) {
				$breadcrumb .= $this->Html->link($involvement['Ministry']['ParentMinistry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['ParentMinistry']['id']));
				$breadcrumb .= ' > ';
			}
			$breadcrumb .= $this->Html->link($involvement['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['id']));
			echo $this->Html->tag('td', $breadcrumb);
			echo $this->Html->tag('td', $this->Html->link($involvement['Involvement']['name'], array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])).$this->Formatting->flags('Involvement', $involvement));
			$date = '&nbsp;';
			if (!empty($involvement['dates'])) {
				$date = $this->Formatting->datetime($involvement['dates'][0]['Date']['start_date'].' '.$involvement['dates'][0]['Date']['start_time']);
			}
			echo $this->Html->tag('td', $date);
			echo '</tr>';
			break;
			}
		endforeach;
		if ($viewStyle == 'list') {
			echo '</tbody></table>';
		}
		?>
	</div>
	<div><?php echo $this->element('pagination'); ?></div>
</div>
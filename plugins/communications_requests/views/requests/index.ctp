<?php
$this->Paginator->options(array(
    'updateable' => 'content'
));
?>
<h1>Requests</h1>
<div class="core-tabs">
	
	<ul>
		<li><a href="#requests">Requests</a></li>
		<?php
		$link = $this->Permission->link('Request Types', array('controller' => 'request_types'), array('title' => 'request-types'));
		echo $link ? $this->Html->tag('li', $link) : null;
		
		$link = $this->Permission->link('Request Statuses', array('controller' => 'request_statuses'), array('title' => 'request-statuses'));
		echo $link ? $this->Html->tag('li', $link) : null;
		?>
	</ul>
	
	<div class="content-box clearfix">
		
		<div id="requests">
			<?php echo $this->MultiSelect->create(); ?>
			<div class="clearfix">
				<?php
				echo $this->Form->create(array(
					'default' => false
				));
				?>
				<fieldset class="grid_5 alpha">
					<legend>Filter</legend>
					<?php
					echo $this->Form->input('Filter.ministry_name');
					echo $this->Form->input('Filter.description');
					?>
				</fieldset>
				<fieldset class="grid_5 alpha">
					<?php
					echo $this->Form->input('Filter.request_status_id', array(
						'empty' => true
					));
					echo $this->Form->input('Filter.request_type_id', array(
						'empty' => true
					));
					?>
				</fieldset>
				<?php
				echo $this->Js->submit('Filter', $defaultSubmitOptions);
				echo $this->Form->end();
				?>
			</div>
			<table class="datatable">
				<thead>
					<?php 
					$checkAll = true;
					$colCount = 9;
					$links = array(
						array(
							'title' => 'Edit',
							'url' => array(
								'plugin' => 'communications_requests',
								'controller' => 'requests',
								'action' => 'edit',
								$this->MultiSelect->token
							),
							'options' => array(
								'rel' => 'modal-content'
							)
						),
						array(
							'title' => 'Remove',
							'url' => array(
								'plugin' => 'communications_requests',
								'controller' => 'requests',
								'action' => 'delete',
								$this->MultiSelect->token
							),
							'options' => array(
								'id' => 'requests-remove'
							)
						)
					);
					$this->Js->buffer('CORE.confirmation("requests-remove", "Are you sure you want to remove the selected requests?", {update:"content"})');
					echo $this->element('multiselect', compact('links', 'colCount', 'checkAll')); 
					?>
					<tr>
						<th>&nbsp;</th>
						<th><?php echo $this->Paginator->sort('Type', 'RequestType.name'); ?></th>
						<th><?php echo $this->Paginator->sort('description'); ?></th>
						<th><?php echo $this->Paginator->sort('ministry_name'); ?></th>
						<th><?php echo $this->Paginator->sort('User', 'Profile.last_name'); ?></th>
						<th><?php echo $this->Paginator->sort('Status', 'RequestStatus.name'); ?></th>
						<th><?php echo $this->Paginator->sort('created'); ?></th>
						<th><?php echo $this->Paginator->sort('Last updated', 'modified'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 0;
					foreach ($requests as $request):
						$class = null;
						if ($i++ % 2 == 0) {
							$class = ' altrow';
						}
					?>
					<tr class="<?php echo $class;?>">
						<td><?php echo $this->MultiSelect->checkbox($request['Request']['id']); ?></td>
						<td><?php echo $request['RequestType']['name']; ?></td>
						<td><?php echo $request['Request']['description']; ?></td>
						<td><?php echo $request['Request']['ministry_name']; ?></td>
						<td><?php echo $request['Profile']['name']; ?></td>
						<td><?php echo $this->Html->link($request['RequestStatus']['name'], array('action' => 'edit', $request['Request']['id']), array('rel' => 'modal-content')); ?></td>
						<td><?php echo $this->Formatting->datetime($request['Request']['created']); ?></td>
						<td><?php echo $this->Formatting->datetime($request['Request']['modified']); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php 
			echo $this->element('pagination');
			echo $this->MultiSelect->end(); ?>
		</div>
		
		<div id="request-types">
			<?php
			$url = Router::url(array('controller' => 'request_types'));
			$this->Js->buffer('CORE.register("requesttypes", "request-types", "'.$url.'");');
			?>
		</div>
		
		<div id="request-statuses">
			<?php
			$url = Router::url(array('controller' => 'request_statuses'));
			$this->Js->buffer('CORE.register("requeststatuses", "request-statuses", "'.$url.'");');
			?>
		</div>
		
	</div>
	
</div>


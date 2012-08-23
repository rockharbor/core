<h1>Leader Dashboards</h1>

<div class="leaders core-tabs">

	<ul>
		<?php
		if ($activeUser['Profile']['leading'] > 0) {
			echo $this->Html->tag('li', $this->Html->link('Involvement', '#involvement-dashboard'));
		}
		if ($activeUser['Profile']['managing'] > 0) {
			echo $this->Html->tag('li', $this->Html->link('Ministry', '#ministry-dashboard'));
		}
		?>
	</ul>
	
	<div class="content-box">
		<?php
		$url = Router::url(array(
			'controller' => 'involvement_leaders',
			'action' => 'dashboard',
			'User' => $activeUser['User']['id']
		));
		?>
		<div id="involvement-dashboard" data-core-update-url="<?php echo $url; ?>">
			<?php 
			if ($activeUser['Profile']['leading'] > 0) {
				echo $this->requestAction($url, array(
					'return',
					'renderAs' => 'ajax'
				));
			}
			?>
		</div>
		<?php
		$url = Router::url(array(
			'controller' => 'ministry_leaders',
			'action' => 'dashboard',
			'User' => $activeUser['User']['id']
		));
		?>
		<div id="ministry-dashboard" data-core-update-url="<?php echo $url; ?>">
			<?php 
			if ($activeUser['Profile']['managing'] > 0) {
				echo $this->requestAction($url, array(
					'return',
					'renderAs' => 'ajax'
				));
			}
			?>
		</div>
	</div>
	
</div>
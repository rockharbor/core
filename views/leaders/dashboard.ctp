<h1>Leader Dashboards</h1>

<div class="leaders core-tabs">

	<ul>
		<?php
		if ($activeUser['Profile']['leading'] > 0) {
			$link = $this->Permission->link('Involvement', array('controller' => 'involvement_leaders', 'action' => 'dashboard', 'User' => $activeUser['User']['id']), array('title' => 'involvement-dashboard'));
			echo $link ? $this->Html->tag('li', $link) : null;
		}
		if ($activeUser['Profile']['managing'] > 0) {
			$link = $this->Permission->link('Ministry', array('controller' => 'ministry_leaders', 'action' => 'dashboard', 'User' => $activeUser['User']['id']), array('title' => 'ministry-dashboard'));
			echo $link ? $this->Html->tag('li', $link) : null;
		}
		?>
	</ul>
	
	<div class="content-box">
		<div id="involvement-dashboard">
			<?php 
			if ($activeUser['Profile']['leading'] > 0) {
				echo $this->requestAction('/involvement_leaders/dashboard', array(
					'return',
					'named' => array(
						'User' => $activeUser['User']['id']
					)
				));
			}
			?>
		</div>
		<div id="ministry-dashboard">
			<?php 
			if ($activeUser['Profile']['managing'] > 0) {
				echo $this->requestAction('/ministry_leaders/dashboard', array(
					'return',
					'named' => array(
						'User' => $activeUser['User']['id']
					)
				));
			}
			?>
		</div>
	</div>
	
</div>
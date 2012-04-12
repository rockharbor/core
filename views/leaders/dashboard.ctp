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
		<div id="involvement-dashboard">
			<?php 
			if ($activeUser['Profile']['leading'] > 0) {
				echo $this->requestAction('/involvement_leaders/dashboard', array(
					'return',
					'named' => array(
						'User' => $activeUser['User']['id']
					),
					'renderAs' => 'ajax'
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
					),
					'renderAs' => 'ajax'
				));
			}
			?>
		</div>
	</div>
	
</div>
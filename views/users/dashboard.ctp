<h1>Admin Dashboard</h1>

<div class="profiles core-tabs">

	<ul>
		<?php
		$link = $this->Permission->link('Merge Requests', array('controller' => 'merge_requests', 'model' => 'User'), array('title' => 'merge-requests'));
		echo $link ? $this->Html->tag('li', $link) : null;
		$link = $this->Permission->link('Image Approval', array('controller' => 'images', 'action' => 'approval'), array('title' => 'images'));
		echo $link ? $this->Html->tag('li', $link) : null;
		$link = $this->Permission->link('Ministry Report', array('controller' => 'reports'), array('title' => 'reports'));
		echo $link ? $this->Html->tag('li', $link) : null;
		$link = $this->Permission->link('Payments Report', array('controller' => 'reports', 'action' => 'payments'), array('title' => 'payment-reports'));
		echo $link ? $this->Html->tag('li', $link) : null;
		$link = $this->Permission->link('Alerts', array('controller' => 'alerts', 'action' => 'index'), array('title' => 'alerts'));
		echo $link ? $this->Html->tag('li', $link) : null;
		?>
		<li><a href="#lists">Lists</a></li>
		<?php
		$link = $this->Permission->link('App Settings', array('controller' => 'app_settings'), array('title' => 'app-settings'));
		echo $link ? $this->Html->tag('li', $link) : null;
		?>
	</ul>

	<div class="content-box clearfix">
		<?php if ($this->Permission->check(array('controller' => 'merge_requests'))): ?>
		<div id="merge-requests">
			<?php
			echo $this->requestAction('/merge_requests/index/model:User', array(
				'renderAs' => 'ajax',
				'return'
			));
			?>
		</div>
		<?php endif; ?>
		<?php if ($this->Permission->check(array('controller' => 'images', 'action' => 'approval'))): ?>
		<div id="images">
		</div>
		<?php endif; ?>
		<?php if ($this->Permission->check(array('controller' => 'reports'))): ?>
		<div id="reports">
		</div>
		<?php endif; ?>
		<?php if ($this->Permission->check(array('controller' => 'reports', 'action' => 'payments'))): ?>
		<div id="payment-reports">
		</div>
		<?php endif; ?>
		<?php if ($this->Permission->check(array('controller' => 'alerts'))): ?>
		<div id="alerts">
			<?php
			echo $this->requestAction('/alerts/index', array(
				'renderAs' => 'ajax',
				'return'
			));
			?>
		</div>
		<?php endif; ?>
		<div id="lists">
			<div class="sub-tabs core-tabs">
				<ul>
				<?php	
				foreach ($controllers as $controller) {
					$link = $this->Permission->link(Inflector::humanize($controller), array('controller' => $controller), array('title' => Inflector::slug(Inflector::humanize($controller), '-')));
					if ($link) {
						echo $this->Html->tag('li', $link);
					}
				}
				?>
				</ul>
				<?php	foreach ($controllers as $controller): ?>
				<div id="<?php echo Inflector::slug(Inflector::humanize($controller), '-'); ?>"></div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ($this->Permission->check(array('controller' => 'app_settings'))): ?>
		<div id="app-settings">
			<?php
			echo $this->requestAction('/app_settings/index', array(
				'renderAs' => 'ajax',
				'return'
			));
			?>
		</div>
		<?php endif; ?>
	</div>
</div>
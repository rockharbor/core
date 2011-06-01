<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Core::read('general.site_name_tagless').' '.Core::read('version').' :: '.$title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// vendor css
		$this->AssetCompress->css('jquery.wysiwyg');
		$this->AssetCompress->css('fullcalendar');

		// CORE css
		$this->AssetCompress->css('reset');
		$this->AssetCompress->css('960');
		$this->AssetCompress->css('960-modal');
		$this->AssetCompress->css('font-face');
		$this->AssetCompress->css('menu');
		$this->AssetCompress->css('jquery-ui');
		$this->AssetCompress->css('styles');
		$this->AssetCompress->css('tables');

		$browser = get_browser($_SERVER['HTTP_USER_AGENT']);
		if($browser->browser == 'IE' && $browser->majorver < 9) {
			$this->AssetCompress->css('ie');
		}		
		$this->AssetCompress->css('calendar');

		// google cdn scripts
		$min = Configure::read('debug') == 0 ? '.min' : null;
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery'.$min.'.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui'.$min.'.js');
		echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=false');
		
		// vendor scripts
		$this->AssetCompress->script('jquery.plugins/jquery.form');
		$this->AssetCompress->script('jquery.plugins/jquery.qtip');
		$this->AssetCompress->script('jquery.plugins/jquery.wysiwyg');
		$this->AssetCompress->script('jquery.plugins/jquery.equalheights');
		$this->AssetCompress->script('jquery.plugins/jquery.fullcalendar');
		
		// CORE scripts
		$this->AssetCompress->script('functions');
		$this->AssetCompress->script('global');
		$this->AssetCompress->script('ui');
		$this->AssetCompress->script('form');
		$this->AssetCompress->script('navigation');
		
		// setup
		$this->Js->buffer('CORE.init();');
		$this->Js->buffer('CORE.register("notifications", "nav-notifications", "/notifications/quick")');
		echo $this->Js->writeBuffer();
		//echo $this->AssetCompress->includeAssets(Configure::read('debug') == 0);
		echo $this->AssetCompress->includeAssets(false);
		echo $scripts_for_layout;
	?>
</head>
<body>
	<div class="container_12" id="wrapper">
		<div class="container_12 clearfix" id="header">
			<div class="grid_10 main-nav-menu" id="primary">
				<?php echo $this->element('menu'.DS.'main-nav'); ?>
			</div>
			<div class="grid_2" id="secondary">
				<?php
				echo $this->Html->link('View API', array('controller' => 'api_classes', 'plugin' => 'api_generator'));
				echo ' / ';
				echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'));
				?>
			</div>
		</div>
		<div id="content-container" class="container_12 clearfix">
			<div id="content" class="grid_10 prefix_1 suffix_1">
				<?php echo $this->Session->flash('auth'); ?>
				<?php echo $this->Session->flash(); ?>

				<?php echo $content_for_layout; ?>
			</div>
		</div>
		<div id="footer" class="container_12 clearfix">
			<?php
			echo $this->Html->image('logo-small.png');
			?>
		</div>
	</div>
	<?php
	// write any buffered scripts that were added in the layout
	echo $this->Js->writeBuffer();
	?>
</body>
</html>
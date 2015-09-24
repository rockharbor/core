<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Core::read('general.site_name_tagless').' '.Core::read('version').' :: '.$title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// CORE css
		$this->AssetCompress->css('reset');
		$this->AssetCompress->css('960');
		$this->AssetCompress->css('960-modal');
		$this->AssetCompress->css('font-face');
		$this->AssetCompress->css('menu');
		$this->AssetCompress->css('jquery.qtip');
		$this->AssetCompress->css('jquery-ui');
		$this->AssetCompress->css('styles');
		$this->AssetCompress->css('tables');
		$this->AssetCompress->css('wysiwyg');
		$this->AssetCompress->css('email');

		$this->AssetCompress->css('fullcalendar');
		$this->AssetCompress->css('calendar');

		echo '<!--[if lt IE 10]>'.$this->Html->css('ie').'<![endif]-->';

		// google cdn scripts
		$min = Configure::read('debug') == 0 ? '.min' : null;
		echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery'.$min.'.js');
		echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui'.$min.'.js');

		// vendor scripts
		$this->AssetCompress->script('jquery.plugins/jquery.form');
		$this->AssetCompress->script('jquery.plugins/jquery.qtip');
		$this->AssetCompress->script('jquery.plugins/jquery.equalheights');
		$this->AssetCompress->script('jquery.plugins/jquery.fullcalendar');
		$this->AssetCompress->script('wysiwyg/advanced');
		$this->AssetCompress->script('wysiwyg/wysihtml5-0.3.0.min');

		// CORE scripts
		$this->AssetCompress->script('functions');
		$this->AssetCompress->script('global');
		$this->AssetCompress->script('ui');
		$this->AssetCompress->script('form');
		$this->AssetCompress->script('navigation');

		// setup
		$this->Js->buffer('CORE.init();');
		$element = addslashes(str_replace(array("\r", "\r\n", "\n"), '', $this->element('wysiwyg_toolbar')));
		$this->Js->buffer("CORE.wysiwygToolbar = '$element';", true);
		if ($this->params['plugin']) {
			// set plugin so update system is aware (for magic `/index/page:1` append)
			$this->Js->buffer('CORE.plugin = "'.$this->params['plugin'].'";');
		}

		echo $this->AssetCompress->includeAssets(Configure::read('debug') == 0);
		echo $scripts_for_layout;
		echo $this->Js->writeBuffer();

		// analytics
		$trackingcode = Core::read('general.tracking_code');
		if (!empty($trackingcode)) {
			echo $trackingcode;
		}
		$this->Js->buffer('
		$("#myrh-dialog").dialog({
			autoOpen: false,
			modal: true,
			width: 500
		});
		$("#myrh-dialog-toggle").click(function(e) {
			e.preventDefault();
			$("#myrh-dialog").dialog("open");
		});
		$("#myrh-notice-close").click(function(e) {
			e.preventDefault();
			$("#myrh-notice").toggle("fold");
		});
		');
	?>
</head>
<body>
	<div class="container_12" id="wrapper">
		<div class="container_12 clearfix" id="header">
			<div class="grid_10 main-nav-menu" id="primary">
				<?php echo $this->element('menu'.DS.'main_nav'); ?>
			</div>
			<div class="grid_2" id="secondary">
				<?php
				if (Core::read('notifications.support_email')) {
					echo $this->Html->link('Support', 'mailto:'.Core::read('notifications.support_email'));
					echo ' / ';
				}
				echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout', 'plugin' => false));
				?>
			</div>
		</div>
		<div id="content-container" class="container_12 clearfix">
			<div class="grid_1 alpha">&nbsp;</div>
			<div id="myrh-notice" class="grid_10">
				<style scoped type="text/css">
					#myrh-notice {
						border-left: 4px solid #ffba00;
						font-size: 18px;
						font-family: arial, sans-serif;
						box-shadow: 0 0 3px 2px rgba(0, 0, 0, 0.1);
						color: #000;
						margin-top: 30px;
						padding-left: 10px;
					}
					#myrh-notice a {
						text-decoration: underline;
					}
					#myrh-notice div {
						margin: 10px 0 10px 0;
					}
					#myrh-notice div p {
						margin: 0;
					}
				</style>
				<div class="grid_9 alpha"><p>We're upgrading CORE to myRH on 9/15/2015. <a href="#" id="myrh-dialog-toggle">Click here for more information.</a></p></div>
				<div class="grid_1 omega"><a href="#" id="myrh-notice-close"><span class="core-icon icon-delete">close</span></a></div>
			</div>
			<div class="grid_1 omega">&nbsp;</div>
			<div id="content" class="grid_10 prefix_1 suffix_1" data-core-update-url="<?php echo $this->here; ?>">
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
	<div id="myrh-dialog">
		<style scoped type="text/css">
			#myrh-dialog {
				font-size: 14px;
			}
		</style>
		<p><strong>ROCK</strong>HARBOR has upgraded its Church Management Software. As of 9/15/2015, you'll be able to visit <a href="http://my.rockharbor.org/">my.rockharbor.org</a> to manage your family, sign up for events, and interact with the groups and teams you're a part of.</p>
		<p>If you had a CORE account your information has been automatically migrated to the new software. Once the migration is complete, you can click the <a href="https://rockharbor.ccbchurch.com/w_sign_up.php">Sign Up</a> link on myRH to activate your account. If you run into any trouble, just email <a href="mailto:myrh@rockharbor.org">myrh@rockharbor.org</a> to get help.</p>
		<p>If you already have an event sign-up link for CORE, you can still register for current events using CORE. Any events with registration starting after 9/15 will be using myRH. Talk to the leader of your event or group if you're unsure where to sign up, or email <a href="mailto:myrh@rockharbor.org">myrh@rockharbor.org</a> for more info.</p>
	</div>
	<?php
	// write any buffered scripts that were added in the layout
	echo $this->Js->writeBuffer();
	?>
</body>
</html>

<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
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
		$this->AssetCompress->css('jquery-ui');
		$this->AssetCompress->css('styles');
		$this->AssetCompress->css('public');
		echo '<!--[if lt IE 9]>'.$this->Html->css('ie').'<![endif]-->';

		// google cdn scripts
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.js');

		// vendor scripts
		$this->AssetCompress->script('jquery.plugins/jquery.qtip');
		$this->AssetCompress->script('jquery.plugins/jquery.equalheights');

		// CORE scripts
		$this->AssetCompress->script('functions');
		$this->AssetCompress->script('global');
		$this->AssetCompress->script('ui');
		$this->AssetCompress->script('form');

		// setup
		echo $this->AssetCompress->includeAssets(Configure::read('debug') == 0);
		$this->Js->buffer('CORE.initUI()');
		echo $this->Js->writeBuffer();
		echo $scripts_for_layout;

	?>
</head>
<body>
	<div class="container_12" id="wrapper">
		<div id="content-container" class="container_12 clearfix">
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->Session->flash(); ?>
			<?php echo $content_for_layout; ?>
		</div>
	</div>
</body>
</html>
<h1>App Settings</h1>
<div class="app-settings form ui-tabs core-tabs">
	<ul>
		<?php
		$settings = Core::read();
		ksort($settings);
		unset($settings['hooks']);
		unset($settings['plugin']);
		foreach ($settings as $category => $setting):
		?>
		<li><a href="#<?php echo Inflector::camelize($category); ?>"><?php echo Inflector::humanize($category); ?></a></li>
		<?php endforeach; ?>
	</ul>

<div class="content-box">
	<?php
	foreach ($settings as $category => $settingName):
	?>
	<div id="<?php echo Inflector::camelize($category); ?>">
		<?php
		$i = 0;
		foreach ($appSettings as $appSetting):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			$appSettingName = explode('.', $appSetting['AppSetting']['name']);
			$appSettingName = $appSettingName[1];
			if (!in_array($appSettingName, array_keys($settingName))) {
				continue;
			}
		?>
		<dl<?php echo $class;?> style="margin-bottom:20px">
			<dt><?php echo Inflector::humanize($appSettingName); ?>:</dt>
			<dd><?php echo $appSetting['AppSetting']['description']; ?></dd>
			<dt>Last Modified:</dt>
			<dd><?php echo $this->Formatting->date($appSetting['AppSetting']['modified']); ?> | <?php echo $this->Html->link('Edit', array('action' => 'edit', $appSetting['AppSetting']['id']),array('rel' => 'modal-content'));?></dd>
			<dd><?php
			if (isset($appSetting['AppSetting']['readable_value'])) {
				echo $appSetting['AppSetting']['readable_value'];
			} else {
				echo $appSetting['AppSetting']['value'];
			}
			?>&nbsp;</dd>
		</dl>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>
</div>
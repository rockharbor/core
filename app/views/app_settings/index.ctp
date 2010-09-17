<h1>App Settings</h1>

<?php
$this->Paginator->options(array(
    'update' => '#content', 
    'evalScripts' => true
));
?>
<div class="app-settings form ui-tabs" rel="tabs">
	<ul>
		<?php
		$settings = Core::read();
		ksort($settings);
		foreach ($settings as $category => $setting):
		?>
		<li><a href="#<?php echo Inflector::camelize($category); ?>"><?php echo $category; ?></a></li>
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
		<dl<?php echo $class;?>>
			<dt><?php echo Inflector::humanize($appSettingName); ?> |
			<?php echo $this->Js->link('Edit', array('action' => 'edit', $appSetting['AppSetting']['id']),
				array(
					'rel'=>'modal-content'
				)
			);?>&nbsp;<em>(last modified <?php echo $this->Formatting->date($appSetting['AppSetting']['modified']); ?>)</em></dt>
			<dd><?php echo $appSetting['AppSetting']['description']; ?> 
				</dd>
			<dd><?php
			if (empty($appSetting['AppSetting']['model'])) {
				echo $appSetting['AppSetting']['value'];
			} else {
				if (!empty($appSetting['AppSetting']['value'])) {
					echo ${$appSetting['AppSetting']['model'].'Options'}[$appSetting['AppSetting']['value']];
				}
			}
			?>&nbsp;</dd>
			<dd>&nbsp;</dd>
			<dd class="actions">
				
			</dd>
		</dl>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>
</div>
<?php
/**
 * Creates a select all checkbox within a TR and includes any links sent. Links
 * will automatically be created if the user has permission
 *
 * ### Links
 * - string `title` The title for the link
 * - array `url` Array based url
 * - array `options` Array of options for the link. See HtmlHelper::link()
 * - boolean `permission` A permission to be checked in addition to needing
 *   ACL access to the link
 *
 * @param integer $colCount The number of columns
 * @param boolean $checkAll Whether the user is allowed to see the "check all" box
 * @param array $links Array of links
 */
if (!isset($colCount)) {
	$colCount = 1;
}
if (!isset($checkAll)) {
	$checkAll = false;
}
if (!isset($links)) {
	return;
}
?>
<tr class="multi-select">
	<th><?php
		if ($checkAll) {
			echo $this->MultiSelect->checkbox('all');
		}
		?>
	</th>
	<th colspan="<?php echo $colCount-1; ?>">
	<?php
	foreach ($links as $link) {
		$_default = array(
			'title' => '',
			'url' => '/',
			'options' => array()
		);
		$link = array_merge($_default, $link);
		if (isset($link['permission']) && $link['permission']) {
			echo $this->Html->link($link['title'], $link['url'], $link['options']);
		} else {
			echo $this->Permission->link($link['title'], $link['url'], $link['options']);
		}
	}
	?>
	</th>
</tr>

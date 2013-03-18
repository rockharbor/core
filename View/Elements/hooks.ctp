<?php
$_defaults = array(
	'hook' => null,
	'tag' => 'li',
	'exclude' => array()
);
extract($_defaults, EXTR_SKIP);

if (!$hook) {
	return;
}
if (is_string($hook)) {
	$hooks = Core::getHooks($hook, $exclude);
} else {
	$hooks = $hook;
}

// parent
$parentLink = null;
if (isset($hooks['options'])) {
	if ($this->Permission->check($hooks['options']['url'])) {
		$linkOpts = isset($hooks['options']['options']) ? $hooks['options']['options'] : array();
		if (!isset($linkOpts['id'])) {
			$linkOpts['id'] = strtolower(Inflector::slug($hooks['options']['title'], '-'));
		}
		if (isset($hooks['options']['element'])) {
			$parentLink = $this->element($hooks['options']['element']);
		} else {
			$parentLink = $this->Permission->link($hooks['options']['title'], $hooks['options']['url'], $linkOpts);
		}
	}
	unset($hooks['options']);
}

// children
$children = null;
foreach ($hooks as $hook) {
	$children .= $this->element('hooks', compact('hook', 'tag'));
}

$out = '';
if (!empty($children) && $parentLink) {
	$children = $this->Html->tag('ul', $children);
}
$out = $parentLink.$children;
if ($parentLink) {
	$out = $this->Html->tag('li', $out);
}
echo $out;
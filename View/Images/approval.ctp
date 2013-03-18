<h1>Image Approval</h1>
<div class="clearfix">
<?php
foreach ($images as $image):
	$controller = strtolower(Inflector::underscore($image['Image']['model'].'Images'));
	$sizes = Configure::read('Core.mediafilters.'.strtolower($image['Image']['model']));
	$size = $sizes['m']['fitCrop'][0];
?>
	<div style="width:<?php echo $size;?>px; float:left; margin-right: 10px; margin-bottom: 10px;">
		<?php
		$path = 'm'.DS.$image['Image']['dirname'].DS.$image['Image']['basename'];
		echo $this->Html->tag('div', $this->Media->embed($path, array('restrict' => 'image')), array('style' => 'margin-bottom: 5px;'));
		echo $this->Html->link('Approve', array('controller' => $controller, 'action' => 'approve', $image['Image']['id'], true), array('class' => 'flat-button green', 'id' => 'approve_btn_'.$image['Image']['id']));
		$this->Js->buffer('CORE.confirmation("approve_btn_'.$image['Image']['id'].'", "Are you sure you want to approve this image?", {update: true})');
		echo $this->Html->link('Deny', array('controller' => $controller, 'action' => 'approve', $image['Image']['id'], false), array('class' => 'flat-button red', 'id' => 'delete_btn_'.$image['Image']['id']));
		$this->Js->buffer('CORE.confirmation("delete_btn_'.$image['Image']['id'].'", "Are you sure you want to delete this image?", {update: true})');
		?>
	</div>
<?php endforeach; ?>
</div>
<?php echo $this->element('pagination');
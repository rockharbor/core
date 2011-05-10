<h1>Image Approval</h1>
<div class="clearfix">
<?php
$sizes = Configure::read('Core.mediafilters.user');
$size = $sizes['m']['fitCrop'][0];
foreach ($images as $image):
?>
	<div style="width:<?php echo $size;?>px; float:left; margin-right: 10px; margin-bottom: 10px;">
		<?php
		$path = 'm'.DS.$image['Image']['dirname'].DS.$image['Image']['basename'];
		echo $this->Html->tag('div', $this->Media->embed($path, array('restrict' => 'image')), array('style' => 'margin-bottom: 5px;'));
		echo $this->Html->link('Approve', array('controller' => 'user_images', 'action' => 'approve', $image['Image']['id']), array('class' => 'flat-button green', 'id' => 'approve_btn_'.$image['Image']['id']));
		$this->Js->buffer('CORE.confirmation("approve_btn_'.$image['Image']['id'].'", "Are you sure you want to approve this image?", {update:"content"})');
		echo $this->Html->link('Deny', array('controller' => 'user_images', 'action' => 'delete', $image['Image']['id']), array('class' => 'flat-button red', 'id' => 'delete_btn_'.$image['Image']['id']));
		$this->Js->buffer('CORE.confirmation("delete_btn_'.$image['Image']['id'].'", "Are you sure you want to delete this image?", {update:"content"})');
		?>
	</div>
<?php endforeach; ?>
</div>
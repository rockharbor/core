<?php
$this->Paginator->options(array(
    'update' => '#addresses',
    'evalScripts' => true
));
?>
<h1>Addresses</h1>
<div class="address_list clearfix">
<?php
$i = 0;
foreach ($addresses as $address):
	$i++;
	$class = 'mapset';
	if ($address['Address']['primary']) {
		$class .= ' primary';
	}
	if (!$address['Address']['active']) {
		$class .= ' history';
	}
?>
	<div class="<?php echo $class; ?>">
		<div id="map_<?php echo $address['Address']['id']; ?>" class="map">
			<?php
			$this->GoogleMap->reset();
			$this->GoogleMap->mapType = 'roadmap';
			$this->GoogleMap->addAddresses(array(
				'lat' => $address['Address']['lat'],
				'lng' => $address['Address']['lng'],
				'street' => $address['Address']['address_line_1'].' '.$address['Address']['address_line_2'],
				'city' => $address['Address']['city'],
				'state' => $address['Address']['state'],
				'zip' => $address['Address']['zip'],
			));
			$this->GoogleMap->zoom = 15;
			echo $this->Html->link($this->GoogleMap->image(150, 150), array('controller' => 'reports', 'action' => 'map', 'User', 'User' => $activeUser['User']['id']) , array('escape' => false, 'rel' => 'modal-none'));
			?>
		</div>
		<div id="address_<?php echo $address['Address']['id']; ?>" class="address core-iconable">
			<p><strong><?php echo $address['Address']['name']; ?></strong><br />
				<?php echo $address['Address']['address_line_1'].' '.$address['Address']['address_line_2']; ?><br/>
				<?php echo $address['Address']['city'].', '.$address['Address']['state'].' '.$address['Address']['zip']; ?>
			</p>
			<hr>
			<p>
				<?php
				if (!$address['Address']['primary']) {
					if ($address['Address']['active']) {
						echo $this->Permission->link('Make Primary', array('controller' => 'user_addresses', 'action' => 'primary', 'Address' => $address['Address']['id'], 'User' => $activeUser['User']['id']), array('id' => 'address_primary_'.$i), array('update' => '#content')).'<br />';
						$this->Js->buffer('CORE.confirmation("address_primary_'.$i.'","Are you sure you want to make this address your primary address?", {update:"content"});');
						echo $this->Permission->link('Deactivate', array('controller' => 'user_addresses', 'action' => 'toggle_activity', 0, 'Address' => $address['Address']['id'], 'User' => $activeUser['User']['id']), array('update' => '#content'));
					} else {
						echo $this->Permission->link('Reactivate', array('controller' => 'user_addresses', 'action' => 'toggle_activity', 1, 'Address' => $address['Address']['id'], 'User' => $activeUser['User']['id']), array('update' => '#content'));
					}
				}
				?>
			</p>
			<?php if ($address['Address']['active']): ?>
			<span class="core-icon-container">
			<?php
			echo $this->Permission->link('Edit', array('controller' => 'user_addresses', 'action' => 'edit', $address['Address']['id'], 'User' => $activeUser['User']['id']), array('class' => 'core-icon icon-edit', 'update' => '#content'));
			?>
			</span>
			<?php endif; ?>
		</div>
	</div>

<?php endforeach; ?>
</div>
	<?php

echo $this->Js->link('Add address',
	array(
		'action' => 'add', $model => $modelId),
	array(
		'class' => 'button',
		'update' => '#content'
	)
);

?>
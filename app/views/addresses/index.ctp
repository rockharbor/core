<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
$this->Js->buffer('CORE.fallbackRegister("addresses");');
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
			echo $this->Html->link($this->GoogleMap->image(150, 150), array('controller' => 'reports', 'action' => 'map', $model, $model => $modelId) , array('escape' => false, 'rel' => 'modal-none'));
			?>
		</div>
		<div id="address_<?php echo $address['Address']['id']; ?>" class="address core-iconable">
			<p><strong><?php echo $address['Address']['name']; ?></strong><br />
				<?php echo $address['Address']['address_line_1'].' '.$address['Address']['address_line_2']; ?><br/>
				<?php echo $address['Address']['city'].', '.$address['Address']['state'].' '.$address['Address']['zip']; ?>
			</p>
			<hr />
			<p>
				<?php
				if (!$address['Address']['primary'] || $address['Address']['model'] == 'User') {
					if ($address['Address']['active']) {
						echo $this->Permission->link('Make Primary', array('action' => 'primary', 'Address' => $address['Address']['id'], $model => $modelId, $address['Address']['model'] => $address['Address']['foreign_key']), array('id' => 'address_primary_'.$i)).'<br />';
						$this->Js->buffer('CORE.confirmation("address_primary_'.$i.'","Are you sure you want to make this address the primary address?", {update:"addresses"});');
						echo $this->Permission->link('Deactivate', array('action' => 'toggle_activity', 0, 'Address' => $address['Address']['id'], $model => $modelId, $address['Address']['model'] => $address['Address']['foreign_key']), array('success' => 'CORE.update("addresses", data)'));
					} else {
						echo $this->Permission->link('Delete', array('action' => 'delete', $address['Address']['id'], $model => $modelId, $address['Address']['model'] => $address['Address']['foreign_key']), array('id' => 'delete_address_'.$i)).'<br />';
						$this->Js->buffer('CORE.confirmation("delete_address_'.$i.'","Are you sure you want to delete this address?", {update:"addresses"});');
						echo $this->Permission->link('Reactivate', array('action' => 'toggle_activity', 1, 'Address' => $address['Address']['id'], $model => $modelId, $address['Address']['model'] => $address['Address']['foreign_key']), array('success' => 'CORE.update("addresses", data)'));
					}
				}
				?>
			</p>
			<?php if ($address['Address']['active']): ?>
			<span class="core-icon-container">
			<?php
			$icon = $this->element('icon', array('icon' => 'edit'));
			echo $this->Permission->link($icon, array('action' => 'edit', $address['Address']['id'], $model => $modelId), array('class' => 'no-hover', 'rel' => 'modal-addresses', 'escape' => false));
			if ($address['Address']['model'] != 'User') {
				$icon = $this->element('icon', array('icon' => 'delete'));
				echo $this->Permission->link($icon, array('action' => 'delete', $address['Address']['id'], $model => $modelId), array('id' => 'delete_address_'.$i, 'class' => 'no-hover', 'escape' => false));
				$this->Js->buffer('CORE.confirmation("delete_address_'.$i.'","Are you sure you want to delete this address?", {update:"addresses"});');
			}
			?>
			</span>
			<?php endif; ?>
		</div>
	</div>

<?php endforeach; ?>
</div>
	<?php
if (count($addresses) == 0 || $model == 'User') {
	echo $this->Html->link('Add address', array('action' => 'add', $model => $modelId), array('class' => 'button', 'rel' => 'modal'));
}

?>
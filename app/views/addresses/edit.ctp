<h1>Edit Address</h1>

<div class="addresses">
<?php echo $this->Form->create('Address', array('default'=>false));?>
	<fieldset>
 		<legend><?php printf(__('Edit %s', true), __('Address', true)); ?></legend>
	<?php
		echo $this->Form->hidden('foreign_key');
		echo $this->Form->hidden('model');
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('address_line_1');
		echo $this->Form->input('address_line_2');
		echo $this->Form->input('city');
		echo $this->Form->input('state', array(
			'type' => 'select',
			'options' => $this->SelectOptions->states
		));
		echo $this->Form->input('zip');
	?>
	<div style="position: absolute; right: 0; top: 30px;" class="mapset">
		<div class="map">
		<?php
			$this->GoogleMap->reset();
			$this->GoogleMap->mapType = 'roadmap';
			$this->GoogleMap->addAddresses(array(
				'lat' => $this->data['Address']['lat'],
				'lng' => $this->data['Address']['lng'],
				'street' => $this->data['Address']['address_line_1'].' '.$this->data['Address']['address_line_2'],
				'city' => $this->data['Address']['city'],
				'state' => $this->data['Address']['state'],
				'zip' => $this->data['Address']['zip'],
			));
			$this->GoogleMap->zoom = 12;
			echo $this->GoogleMap->image(300, 300);
		?>
		</div>
	</div>
	</fieldset>
<?php
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
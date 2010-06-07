<h3>Original User</h3>
<div id="merge_original">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Username</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $result['Source']['username']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Name</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $result['Source']['Profile']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Birth Date</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Formatting->date($result['Source']['Profile']['birth_date']); ?>
			&nbsp;
		</dd>
		<?php foreach ($result['Source']['Address'] as $address): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Address</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $address['address_line_1']; ?>
			<?php
			if (!empty($address['address_line_2'])) {
				echo '<br />'.$address['address_line_2'];
			}
			?><br />
			<?php echo $address['city']; ?>, <?php echo $address['state']; ?> <?php echo $address['zip']; ?>
			&nbsp;
		</dd>
		<?php endforeach; ?>
	</dl>
</div>

<h3>New Details</h3>
<p>The old information will be merged with these details, with this information taking precedence.</p>
<div id="merge_original">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Username</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $result['Target']['username']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Name</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $result['Target']['Profile']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Birth Date</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Formatting->date($result['Target']['Profile']['birth_date']); ?>
			&nbsp;
		</dd>
		<?php foreach ($result['Target']['Address'] as $address): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>Address</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $address['address_line_1']; ?>
			<?php
			if (!empty($address['address_line_2'])) {
				echo '<br />'.$address['address_line_2'];
			}
			?><br />
			<?php echo $address['city']; ?>, <?php echo $address['state']; ?> <?php echo $address['zip']; ?>
			&nbsp;
		</dd>
		<?php endforeach; ?>
	</dl>
</div>
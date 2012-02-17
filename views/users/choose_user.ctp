<h1>Found Multiple Matches</h1>
<p>Multiple matches for that user were found in <?php echo Core::read('general.site_name'); ?>! Please select one below.</p>
<?php
$data = Set::flatten($this->data);
$posteddata = '';
foreach ($data as $field => $value) {
	if (strstr($field, '_Token') !== false) {
		continue;
	}
	$this->Form->__secure($field, $value);
	$field = explode('.', $field);
	$fieldname = 'data['.implode('][', $field).']';
	$posteddata .= "<input type=\"hidden\" value=\"$value\" name=\"$fieldname\" />";
}
$secureFields = $this->Form->fields;
?>
<table cellpadding="0" cellspacing="0" class="datatable">
	<thead>
		<tr>
			<th>First Name</th>
			<th>Last Name</th>
			<th>City</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 0;
		foreach ($users as $user):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
			<tr<?php echo $class;?>>
				<td><?php echo $user['Profile']['first_name']; ?></td>
				<td><?php echo $user['Profile']['last_name']; ?></td>
				<td><?php echo $user['ActiveAddress']['city']; ?></td>
				<td><?php
				$url = String::insert(
					$redirect,
					array(
						'ID' => $user['User']['id']
					),
					array(
						'after' => ':'
					)
				);
				echo $this->Form->create(array(
					'url' => $url,
					'default'=> false
				));
				$this->Form->fields = $secureFields;
				echo $posteddata;
				$defaultSubmitOptions['url'] = $url;
				echo $this->Js->submit('Choose', $defaultSubmitOptions);
				echo $this->Form->end();
				?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<p><?php
echo $this->Form->create(null, array(
	'url' => $return,
	'default'=> false
));
$this->Form->fields = $secureFields;
echo $posteddata;
$defaultSubmitOptions['url'] = $return;
echo $this->Js->submit('None of these match', $defaultSubmitOptions);
echo $this->Form->end();
?></p>
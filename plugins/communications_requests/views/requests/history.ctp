<h1>Request History</h1>
<table class="datatable">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Type', 'RequestType.name'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('ministry_name'); ?></th>
			<th><?php echo $this->Paginator->sort('budget'); ?></th>
			<th><?php echo $this->Paginator->sort('Status', 'RequestStatus.name'); ?></th>
			<th><?php echo $this->Paginator->sort('Last updated', 'modified'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$i = 0;
		foreach ($requests as $request):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' altrow';
			}
		?>
		<tr class="<?php echo $class;?>">
			<td><?php echo $request['RequestType']['name']; ?></td>
			<td><?php echo $request['Request']['description']; ?></td>
			<td><?php echo $request['Request']['ministry_name']; ?></td>
			<td><?php echo $this->Formatting->money($request['Request']['budget']); ?></td>
			<td><?php echo $request['RequestStatus']['name']; ?></td>
			<td><?php echo $this->Formatting->datetime($request['Request']['modified']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->element('pagination'); ?>
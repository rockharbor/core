<h1><?php __('Covenants'); ?></h1>
<div class="covenants content-box">
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th>Year</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach($covenants as $covenant):
				$class = null;
				if ($i++ % 2 == 0)
					$class = "altrow";
			?>
			<tr class="core-iconable <?php echo $class; ?>">
				<td><?php echo $covenant['Covenant']['year']; ?></td>
				<td width="20px">
					<div class="core-icon-container">
						<?php
						$icon = $this->element('icon', array('icon' => 'delete'));
						echo $this->Permission->link($icon, array('action' => 'delete', $covenant['Covenant']['id'], 'User' => $userId), array('id' => 'delete-covenant-btn-'.$covenant['Covenant']['id'], 'class' => 'no_hover', 'escape' => false));
						$this->Js->buffer('CORE.confirmation("delete-covenant-btn-'.$covenant['Covenant']['id'].'", "Are you sure you want to delete this covenant?");');
						?>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p>
		<?php echo $this->Permission->link('New Covenant', array('action' => 'add', 'User' => $userId), array('data-core-modal' => 'true', 'class' => 'button')); ?>
	</p>
</div>

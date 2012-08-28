<?php 
echo $this->Html->script('misc/involvement', array('inline' => false));
?>

<span class="breadcrumb"><?php
echo $this->Html->link($involvement['Ministry']['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $involvement['Ministry']['Campus']['id']), array('escape' => false));
echo ' > ';
if (!empty($involvement['Ministry']['ParentMinistry']['id'])) {
	echo $this->Html->link($involvement['Ministry']['ParentMinistry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['ParentMinistry']['id']), array('escape' => false));
	echo ' > ';
}
echo $this->Html->link($involvement['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['id']), array('escape' => false));
?></span>
<h1><?php echo $involvement['Involvement']['name'].$this->Formatting->flags('Involvement', $involvement); ?></h1>
<div class="involvements view core-tabs">
	<ul>
		<li><a href="#details-tab">Details</a></li>
		<?php 
		if ($canSeeRoster) {
			$link = $this->Html->link('Roster', '#roster-tab');
			echo $this->Html->tag('li', $link);
		}
		?>
	</ul>

	<div class="content-box clearfix">
		<div id="details-tab">
			<div id="columns" class="grid_10 alpha omega clearfix">
				<?php
				if (!empty($involvement['Image'])): ?>
				<div class="grid_4 alpha">
					<?php
					$path = 'm'.DS.$involvement['Image'][0]['dirname'].DS.$involvement['Image'][0]['basename'];
					echo $this->Media->embed($path, array('restrict' => 'image'));
					?>
				</div>
				<?php endif; ?>
				<?php if (!empty($involvement['Date'])): ?>
				<div id="involvement-dates">
					<?php foreach($involvement['Date'] as $date): ?>
					<div class="date clearfix">
						<div class="grid_2 column border-right alpha">
							<span class="font-large">
							<?php
								echo strtoupper(date('M. j', strtotime($date['Date']['start_date'])));
								echo '<br />';
								echo date('Y', strtotime($date['Date']['start_date']));
							?>
							</span>
						</div>
						<div class="grid_2 column border-right">
							<span class="font-large">
							<?php
							if (!$date['Date']['all_day']) {
								$icons = array();
								$meridian = date('a', strtotime($date['Date']['start_time']));
								$icons[] = $this->element('icon', array('icon' => $meridian.'-on'));
								$meridian = $meridian == 'am' ? 'pm' : 'am';
								$icons[] = $this->element('icon', array('icon' => $meridian));
								sort($icons);
								echo date('h:i', strtotime($date['Date']['start_time']));
								echo $this->Html->tag('span', implode('<br />', $icons), array('style' => 'display:inline-block;'));
							} else {
								echo 'All Day';
							}
							?>
							</span>
						</div>
						<div class="grid_2 column omega">
							<?php
							if (isset($date['Date']['original'])) {
								// recurring
								echo $this->Html->tag('p', $this->Formatting->readableDate($date['Date']['original']));
							} else {
								// non-recurring
								echo $this->Html->tag('p', $this->Formatting->readableDate($date));
							}
							?>
						</div>
					</div>
					<?php endforeach; ?>
					<?php if (count($involvement['Date']) > 1):
					$prefix = null;
					if (!empty($involvement['Image'])) {
						$prefix = ' prefix_4';
					}
					?>
					<div class="more-dates grid_2 clearfix <?php echo $prefix; ?>" style="text-align:center">
						<div class="pagination">
							<?php 
							for ($i=0; $i<count($involvement['Date']); $i++) {
								$icon = $this->element('icon', array('icon' => 'bullet'));
								echo $this->Html->link($icon, array('controller' => 'dates', 'Involvement' => $involvement['Involvement']['id']), array('escape' => false, 'class' => 'no-hover', 'target' => $i));
							}
							?>
						</div>
						<?php
						$link = $this->Html->link('See all dates', array('controller' => 'dates', 'Involvement' => $involvement['Involvement']['id']), array('data-core-modal' => '{"update":false}'));
						echo $this->Html->tag('small', $link);
						?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<div class="grid_10 alpha omega">
				<div class="grid_6 alpha">
					<h3>Description</h3>
					<p><?php echo $involvement['Involvement']['description']; ?></p>
				</div>
				<div class="grid_4 omega">
					<?php if (!empty($involvement['Leader'])): ?>
					<h3>Leaders</h3>
					<div class="box">
						<?php
						$leaders = array();
						foreach ($involvement['Leader'] as $leader) {
							echo '<div class="core-iconable">';
							$icon = $this->Html->tag('span', 'Email', array('class' => 'core-icon icon-email'));
							echo $icon.$this->Html->link($leader['User']['Profile']['name'], array('controller' => 'sys_emails', 'action' => 'leader', $leader['id']), array('data-core-modal' => '{"update":false}'));
							if (!empty($leader)) {
								$icon = $this->element('icon', array('icon' => 'delete'));
								$link = $this->Permission->link($icon, array('controller' => 'involvement_leaders', 'action' => 'delete', 'Involvement' => $involvement['Involvement']['id'], 'User' => $leader['User']['id']), array('id' => 'remove-leader-'.$leader['User']['id'], 'escape' => false, 'class' => 'no-hover'));
								if ($link) {
									$this->Js->buffer('CORE.confirmation("remove-leader-'.$leader['User']['id'].'", "Are you sure you want to remove '.$leader['User']['Profile']['name'].' from leading?", {update:true})');
									echo $this->Html->tag('div', $link, array('class' => 'core-icon-container'));
								}
							}
							echo '</div>';
						}
						?>
					</div>
					<?php endif; ?>
					<?php if (!empty($involvement['Address']) && !empty($involvement['Address']['id'])): ?>
					<h3>Address</h3>
					<div class="box"><?php echo $this->Formatting->address($involvement['Address']); ?></div>
					<?php endif; ?>
					<?php if (!empty($signedUp)): ?>
					<h3>Signed Up</h3>
					<div class="box highlight">
						<?php	
						foreach ($signedUp as $householdMember) {
							echo '<div class="core-iconable">';
							echo $this->Html->link($householdMember['User']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $householdMember['User']['id']), array('style' => 'display:block'));
							$links = array();
							$icon = $this->element('icon', array('icon' => 'edit'));
							$link = $this->Permission->link($icon, array('controller' => 'rosters', 'action' => 'edit', $householdMember['Roster']['id'], 'User' => $householdMember['User']['id']), array('data-core-modal' => '{&quot;update&quot;:false}', 'class' => 'no-hover', 'escape' => false));
							if ($link) {
								$links[] = $link;
							}
							$icon = $this->element('icon', array('icon' => 'delete'));
							$link = $this->Permission->link($icon, array('controller' => 'rosters', 'action' => 'delete', $householdMember['Roster']['id'], 'User' => $householdMember['User']['id']), array('class' => 'no-hover', 'escape' => false, 'id' => 'remove-'.$householdMember['Roster']['id']));
							if ($link) {
								$links[] = $link;
								$this->Js->buffer('CORE.confirmation("remove-'.$householdMember['Roster']['id'].'", "Are you sure you want to remove '.$householdMember['User']['Profile']['name'].'?", {update:true})');
							}
							if (!empty($links)) {
								echo $this->Html->tag('div', implode('', $links), array('class' => 'core-icon-container'));
							}
							echo '</div>';
						} ?>
					</div>
					<?php endif; ?>
					<?php if (!empty($involvement['Document'])): ?>
					<h3>Documents</h3>
					<div class="box">
						<?php
						$documents = array();
						foreach ($involvement['Document'] as $document) {
							$icon = $this->Html->tag('span', 'Download', array('class' => 'core-icon icon-download'));
							$documents[] = $icon.$this->Html->link($document['alternative'], array('controller' => 'involvement_documents', 'action' => 'download', $document['id']));
						}
						echo implode('<br />', $documents);
						?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="grid_10 alpha omega">
				<?php 
				if ($involvement['Involvement']['signup'] && $involvement['Involvement']['active'] && !$involvement['Involvement']['previous'] && !$inRoster) {
					if (!$full) {
						echo $this->Html->link('Sign up', array('controller' => 'rosters', 'action' => 'add', 'User' => $activeUser['User']['id'], 'Involvement' => $involvement['Involvement']['id']), array('data-core-modal' => 'true', 'class' => 'button'));
					} else {
						echo $this->Html->link('Roster full', array('controller' => 'rosters', 'action' => 'add', 'User' => $activeUser['User']['id'], 'Involvement' => $involvement['Involvement']['id']), array('data-core-modal' => 'true', 'class' => 'button disabled'));
					}
				}
				?>
			</div>
			<ul class="core-admin-tabs">
				<?php
				$link = $this->Permission->link('Edit', array('action' => 'edit', 'Involvement' => $involvement['Involvement']['id']));
				if ($link) {
					echo $this->Html->tag('li', $link);
				}
				$link = $this->Permission->link('Delete', array('action' => 'delete', 'Involvement' => $involvement['Involvement']['id']), array('id' => 'delete_btn'));
				if ($link) {
					$this->Js->buffer('CORE.confirmation("delete_btn", "Are you sure you want to delete this '.$involvement['InvolvementType']['name'].' and all it\'s related content?", {update:true});');
					echo $this->Html->tag('li', $link);
				}
				?>
			</ul>
		</div>
		<?php if ($canSeeRoster): ?>
		<?php
		$url = Router::url(array(
			'controller' => 'rosters',
			'action' => 'index',
			'Involvement' => $involvement['Involvement']['id']
		));
		?>
		<div id="roster-tab" data-core-update-url="<?php echo $url; ?>">
			<?php 
			echo $this->requestAction($url, array(
				'return',
				'renderAs' => 'ajax'
			));
			?>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php
$this->Js->buffer('CORE_involvement.setup()');

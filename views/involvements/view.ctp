<?php 
echo $this->Html->script('super_date', array('inline' => false));
echo $this->Html->script('misc/involvement', array('inline' => false));
?>

<span class="breadcrumb"><?php
echo $this->Html->link($involvement['Ministry']['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $involvement['Ministry']['Campus']['id']), array('escape' => false));
echo ' > ';
echo $this->Html->link($involvement['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['id']), array('escape' => false));
?></span>
<h1><?php echo $involvement['Involvement']['name'].$this->Formatting->flags('Involvement', $involvement); ?></h1>
<div class="involvements view core-tabs">
	<ul>
		<li><a href="#details-tab">Details</a></li>
		<?php if ($this->Permission->can('viewRoster')) { ?>
		<li><?php echo $this->Html->link('Roster', array('controller' => 'rosters', 'Involvement' => $involvement['Involvement']['id']), array('title' => 'roster-tab')); ?></li>
		<?php } ?>
	</ul>

	<div class="content-box clearfix">
		<div id="details-tab">
			<div id="columns" class="grid_10 alpha omega clearfix">
				<?php
				$first = ' alpha';
				if (!empty($involvement['Ministry']['Image'])) { ?>
				<div class="grid_4<?php echo $first; ?>">
					<?php
					$path = 'l'.DS.$involvement['Ministry']['Image'][0]['dirname'].DS.$involvement['Ministry']['Image'][0]['basename'];
					echo $this->Media->embed($path, array('restrict' => 'image'));
					?>
				</div>
				<?php 
				$first = '';
				} ?>
				<?php if (!empty($involvement['Date'])) { ?>
				<div id="involvement-dates">
					<?php foreach($involvement['Date'] as $date): ?>
					<div class="date clearfix">
						<div class="grid_2 column border-right<?php echo $first; ?>">
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
								echo $this->Formatting->readableDate($date['Date']['original']);
							} else {
								// non-recurring
								echo $this->Formatting->readableDate($date);
							}
							?>
						</div>
					</div>
					<?php endforeach; ?>
					<?php if (count($involvement['Date']) > 1): ?>
					<div class="grid_2 clearfix alpha" style="text-align:center">
						<div class="pagination">
							<?php 
							for ($i=0; $i<count($involvement['Date']); $i++) {
								$icon = $this->element('icon', array('icon' => 'bullet'));
								echo $this->Html->link($icon, array('controller' => 'dates', 'Involvement' => $involvement['Involvement']['id']), array('escape' => false, 'class' => 'no-hover', 'target' => $i));
							}
							?>
						</div>
						<?php
						$link = $this->Html->link('See all dates', array('controller' => 'dates', 'Involvement' => $involvement['Involvement']['id']), array('rel' => 'modal-none'));
						echo $this->Html->tag('small', $link);
						?>
					</div>
					<?php endif; ?>
				</div>
				<?php 
				$first = '';
				} ?>
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
							$icon = $this->Html->tag('span', 'Email', array('class' => 'core-icon icon-email'));
							$leaders[] = $icon.$this->Html->link($leader['User']['Profile']['name'], array('controller' => 'sys_emails', 'action' => 'compose', 'model' => 'User', 'User' => $leader['User']['id']), array('rel' => 'modal-none'));
						}
						echo implode('<br />', $leaders);
						?>
					</div>
					<?php endif; ?>
					<?php if (!empty($involvement['Address']) && !empty($involvement['Address']['id'])): ?>
					<h3>Address</h3>
					<div class="box"><?php echo $this->Formatting->address($involvement['Address']); ?></div>
					<?php endif; ?>
					<?php if (!empty($involvement['Roster'])): ?>
					<h3>Signed Up</h3>
					<div class="box highlight">
						<?php	foreach ($involvement['Roster'] as $householdMember) {
							echo $this->Html->link($householdMember['User']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $householdMember['User']['id']), array('style' => 'display:block'));
						} ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="grid_10 alpha omega">
				<?php 
				if ($involvement['Involvement']['signup'] && $involvement['Involvement']['active'] && !$involvement['Involvement']['passed']) {
					echo $this->Html->link('Sign up', array('controller' => 'rosters', 'action' => 'add', 'User' => $activeUser['User']['id'], 'Involvement' => $involvement['Involvement']['id']), array('rel' => 'modal-content', 'class' => 'button'));
				}		
				?>
			</div>
			<ul class="core-admin-tabs">
				<?php
				$link = $this->Permission->link('Edit', array('action' => 'edit', 'Involvement' => $involvement['Involvement']['id']));
				if ($link) {
					echo $this->Html->tag('li', $link);
				}
				?>
			</ul>
		</div>
		<?php if ($this->Permission->can('viewRoster')) { ?>
		<div id="roster-tab">
			<?php
			echo $this->requestAction('/rosters/index', array(
				'named' => array(
					'Involvement' => $involvement['Involvement']['id']
				),
				'return',
				'respondAs' => 'ajax'
			));
			$this->Js->buffer('CORE.register("roster", "roster-tab", "/rosters/index/Involvement:'.$involvement['Involvement']['id'].'")');
			?>
		</div>
		<?php } ?>
	</div>
</div>

<?php
$this->Js->buffer('CORE_involvement.setup()');
?>
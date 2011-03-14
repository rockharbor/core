<?php echo $this->Html->script('super_date', array('inline' => false)); ?>

<span class="breadcrumb"><?php
echo $this->Html->link($involvement['Ministry']['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $involvement['Ministry']['Campus']['id']), array('escape' => false));
echo ' > ';
echo $this->Html->link($involvement['Ministry']['name'], array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $involvement['Ministry']['id']), array('escape' => false));
?></span>
<h1><?php echo $involvement['Involvement']['name']; ?></h1>
<div class="involvements view core-tabs">
	<ul>
		<li><a href="#details-tab">Details</a></li>
		<?php if ($this->Permission->can('viewRoster')) { ?>
		<li><?php echo $this->Html->link('Roster', array('controller' => 'rosters', 'Involvement' => $involvement['Involvement']['id']), array('title' => 'roster-tab')); ?></li>
		<?php } ?>
	</ul>

	<div class="content-box clearfix">
		<div id="details-tab">
			<div id="columns" class="grid_10 alpha omega">
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
				<div class="grid_2 column border-right<?php echo $first; ?>" id="date">
					<span class="font-large">
					<?php
						echo strtoupper(date('M. j', strtotime($involvement['Date'][0]['Date']['start_date'])));
						echo '<br />';
						echo date('Y', strtotime($involvement['Date'][0]['Date']['start_date']));
					?>
					</span>
				</div>
				<div class="grid_2 column border-right" id="time">
					<span class="font-large">
					<?php
						echo date('h:i', strtotime($involvement['Date'][0]['Date']['start_time']));
					?>
					</span>
				</div>
				<div class="grid_2 column omega" id="readable-date">
					<?php
						echo $this->Formatting->readableDate($involvement['Date'][0]);
					?>
				</div>
				<?php 
				$first = '';
				} ?>
			</div>
			<div class="grid_10 alpha omega">
				<div class="grid_6 alpha">
					<h3>Description</h3>
					<p><?php echo html_entity_decode($involvement['Involvement']['description']); ?></p>
				</div>
				<div class="grid_4 omega">
					<?php if (!empty($involvement['Leader'])) { ?>
					<h3>Leaders</h3>
					<p>
						<?php
						foreach ($involvement['Leader'] as $leader) {
							$icon = $this->Html->tag('span', 'Email', array('class' => 'core-icon icon-email'));
							echo $icon.$this->Html->link($leader['User']['Profile']['name'], array('controller' => 'sys_emails', 'action' => 'compose', 'model' => 'User', 'User' => $leader['User']['id']), array('rel' => 'compose'));
							echo '<br />';
						}
					echo '</p>';
					} ?>					
					<?php if (!empty($involvement['Address'])) { ?>
					<h3>Address</h3>
						<?php
						$address = $involvement['Address']['address_line_1'];
						$address .= $involvement['Address']['address_line_2'];
						$address .= '<br />';
						$address .= $involvement['Address']['city'].', ';
						$address .= $involvement['Address']['state'];
						$address .= $involvement['Address']['zip'];
						echo $this->Html->link($address, array('controller' => 'addresses', 'action' => 'view', $involvement['Address']['id']), array('rel' => 'modal-none', 'class' => 'icon-map'));
					} ?>
				</div>
			</div>
			<div class="grid_10 alpha omega">
				<?php 
				if ($involvement['Involvement']['signup']) {
					echo $this->Html->link('Sign up', array('controller' => 'rosters', 'action' => 'add', 'User' => $activeUser['User']['id'], 'Involvement' => $involvement['Involvement']['id']), array('rel' => 'modal-content', 'class' => 'button'));
				}		
				?>
			</div>
			<ul class="core-admin-tabs">
				<li><?php echo $this->Html->link('Edit Involvement', array('action' => 'edit', 'Involvement' => $involvement['Involvement']['id'])); ?> </li>
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

echo $this->Html->scriptBlock('
function inviteUser(userid) {
	CORE.request("'.Router::url(array('controller' => 'involvements', 'action' => 'invite')).'/"+userid+"/Involvement:'.$involvement['Involvement']['id'].'");
}
function inviteRoster(involvementid) {
	CORE.request("'.Router::url(array('controller' => 'involvements', 'action' => 'invite_roster')).'/"+involvementid+"/Involvement:'.$involvement['Involvement']['id'].'");
}

');

?>
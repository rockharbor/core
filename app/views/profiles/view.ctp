<h1><?php echo $profile['Profile']['name']; ?></h1>

<div class="profiles core-tabs">

	<ul>
		<li><a href="#my-profile">My Profile</a></li>
		<li><?php echo $this->Html->link('My Household', array('controller' => 'households', 'User' => $profile['User']['id']), array('title' => 'household')); ?></li>
		<li><?php echo $this->Html->link('Payments', array('controller' => 'payments', 'User' => $profile['User']['id']), array('title' => 'payments')); ?></li>
	</ul>

	<div class="content-box clearfix">
		<div id="my-profile">
			<div class="grid_10 alpha omega">
				<div class="grid_2 alpha">
				<?php
					$path = null;
					$upload = false;
					if (!empty($profile['Image'])) {
						$path = 'm'.DS.$profile['Image'][0]['dirname'].DS.$profile['Image'][0]['basename'];
					} else {
						$default = Core::read('user.default_image');
						if ($default) {
							$path = 'm'.DS.$default['Image']['dirname'].DS.$default['Image']['basename'];
						}
						$upload = true;
					}
					echo '<div id='.$profile['User']['id'].'Image'.'>';
					echo $this->Media->embed($path, array('restrict' => 'image'));					
					if ($upload) {
						echo $this->element('upload', array(
							'type' => 'image',
							'model' => 'User',
							'User' => $profile['User']['id'],
							'title' => 'Upload Photo',
							'update' => $profile['User']['id'].'Image'
						));
					} else {
						echo $this->Js->link('Remove Photo', array('controller' => 'user_images', 'action' => 'delete', $profile['Image'][0]['id'], 'User' => $profile['User']['id']), array('class' => 'button', 'update' => '#'.$profile['User']['id'].'Image'));
					}
					echo '</div>';
				?>
				</div>

				<div class="grid_3">
					<dl>
					<?php
					echo $this->Html->tag('dt', 'Username:');
					echo $this->Html->tag('dd', $profile['User']['username'].$this->Formatting->flags('User', $profile));
					?>
					</dl>
					<hr>
					<p class="core-iconable">
					<?php
					echo $this->Formatting->address($profile['ActiveAddress'], $profile['User']['id']);
					?>
						<span class="core-icon-container">
						<?php
						echo $this->Permission->link('Edit', array('controller' => 'user_addresses', 'action' => 'index', 'User' => $profile['User']['id']), array('class' => 'core-icon icon-edit', 'rel' => 'modal-content', 'title' => 'View Addresses'));
						?>
						</span>
					</p>
					<hr>
					<p>
					<?php $emails = array();
					$emails[] = $profile['Profile']['primary_email'];
					$emails[] = $profile['Profile']['alternate_email_1'];
					$emails[] = $profile['Profile']['alternate_email_2'];
					$emails = array_filter($emails);
					foreach ($emails as &$email) {
						$email = $this->Formatting->email($email, $profile['User']['id']);
					}
					echo implode('<br />', $emails);
					?>
					</p>
				</div>
				<div class="grid_5 omega">
					<dl>
						<?php
						$phones = array(
							'Cell:' => 'cell_phone',
							'Home:' => 'home_phone',
							'Office:' => 'work_phone'
						);
						foreach ($phones as $title => $phone) {
							if (!empty($profile['Profile'][$phone])) {
								echo $this->Html->tag('dt', $title);
								$ext = isset($profile['Profile'][$phone.'_ext']) ? $profile['Profile'][$phone.'_ext'] : null;
								echo $this->Html->tag('dd', $this->Formatting->phone($profile['Profile'][$phone], $ext));
							}
						}
						?>
					</dl>
					<hr>
					<dl>
						<?php
							echo $this->Html->tag('dt', 'Birthday:');
							echo $this->Html->tag('dd', $this->Formatting->date($profile['Profile']['birth_date']));
							echo $this->Html->tag('dt', 'Age:');
							echo $this->Html->tag('dd', $this->Formatting->age($profile['Profile']['age']));
							echo $this->Html->tag('dt', 'Gender:');
							echo $this->Html->tag('dd', $this->SelectOptions->gender($profile['Profile']['gender']));
							echo $this->Html->tag('dt', 'Classification:');
							$cl = $classifications[$profile['Profile']['classification_id']];
							$cl .= ' @ ';
							$cl .= $this->Html->link($campuses[$profile['Profile']['campus_id']], array('controller' => 'campuses', 'action' => 'view', 'Campus' => $profile['Profile']['campus_id']));
							echo $this->Html->tag('dd', $cl);
						?>
					</dl>
				</div>
			</div>
			<div class="grid_10 alpha omega">
				<?php echo $this->Html->link('More', 'javascript:;', array('style' => 'float:right', 'class' => 'button', 'id' => 'details-more')); ?>
				<hr style="margin-bottom:0;">
				<div id="details-toggle">
					<div class="grid_5 alpha">
						<dl>
						<?php
							echo $this->Html->tag('dt', 'Marital Status:');
							echo $this->Html->tag('dd', $this->SelectOptions->maritalStatus($profile['Profile']['marital_status']));
							echo $this->Html->tag('dt', 'Job Category:');
							echo $this->Html->tag('dd', $this->SelectOptions->value('Profile.job_category_id', $profile));
							echo $this->Html->tag('dt', 'Occupation:');
							echo $this->Html->tag('dd', $profile['Profile']['occupation']);
							echo $this->Html->tag('dt', 'Accepted Christ:');
							$ac = '';
							if ($profile['Profile']['accepted_christ']) {
								$ac .= 'Yes';
								if (!empty($profile['Profile']['accepted_christ_year'])) {
									$ac .= ', '.$profile['Profile']['accepted_christ_year'];
								}
							} else {
								$ac .= 'No';
							}
							echo $this->Html->tag('dd', $ac);
							echo $this->Html->tag('dt', 'Baptized:');
							$bt = '';
							if (!empty($profile['Profile']['baptism_date'])) {
								$bt .= 'Yes, '.$this->Formatting->date($profile['Profile']['baptism_date']);
							} else {
								$bt = 'No';
							}
							echo $this->Html->tag('dd', $bt);
						?>
						</dl>
					</div>
					<div class="grid_5 omega">
						<dl>
						<?php
							echo $this->Html->tag('dt', 'Grade School:');
							echo $this->Html->tag('dd', $this->SelectOptions->value('Profile.elementary_school_id', $profile));
							echo $this->Html->tag('dt', 'Middle School:');
							echo $this->Html->tag('dd', $this->SelectOptions->value('Profile.middle_school_id', $profile));
							echo $this->Html->tag('dt', 'High School:');
							echo $this->Html->tag('dd', $this->SelectOptions->value('Profile.high_school_id', $profile));
							echo $this->Html->tag('dt', 'College:');
							echo $this->Html->tag('dd', $this->SelectOptions->value('Profile.college_id', $profile));
							echo $this->Html->tag('dt', 'Grade:');
							echo $this->Html->tag('dd', $this->SelectOptions->grade($profile['Profile']['grade']));
							echo $this->Html->tag('dt', 'Graduation Year:');
							echo $this->Html->tag('dd', $profile['Profile']['graduation_year']);
						?>
						</dl>
					</div>
				</div>
				<br />
				<?php
				$this->Js->buffer('$("#details-toggle").hide()');
				$this->Js->buffer('$("#details-more").click(function() {
					$("#details-toggle").slideToggle("slow", function() {
						if ($("#details-toggle").css("display") == "none") {
							$("#details-more").button("option", "label", "More");
						} else {
							$("#details-more").button("option", "label", "Less");
						}
					});
				})');
				?>
			</div>
			<div class="grid_10 alpha omega">
				<div class="grid_7 alpha">
					<h3>My Involvement</h3>
				</div>
				<div class="grid_3 omega">
					<h3>Calendar</h3>
				</div>
			</div>
			<div class="grid_10 alpha omega">
				<div class="grid_7 alpha">
					<div id="involvement">
						<?php
						$this->Js->buffer('CORE.register("involvement", "involvement", "/rosters/involvement/User:'.$profile['User']['id'].'")');
						echo $this->requestAction('/rosters/involvement', array(
							'renderAs' => 'ajax',
							'bare' => false,
							'return',
							'named' => array(
								'User' => $profile['User']['id']
							),
							'data' => null,
							'form' => array('data' => null)
							));
						?>
					</div>
				</div>
				<div class="grid_3 omega">
					<div id="calendar">
						<?php
						$this->Js->buffer('CORE.register("calendar", "calendar", "/dates/calendar/model:User/User:'.$profile['User']['id'].'")');
						echo $this->requestAction('/dates/calendar', array(
							'renderAs' => 'ajax',
							'bare' => false,
							'return',
							'named' => array(
								'model' => 'User',
								'User' => $profile['User']['id']
							),
							'data' => null
						));
						?>
					</div>
				</div>
			</div>

			<ul class="core-admin-tabs">
				<li><?php echo $this->Permission->link('Administration', array('controller' => 'profiles', 'action' => 'admin', 'User' => $profile['User']['id']), array('rel' => 'modal-none')); ?></li>
				<li><?php echo $this->Permission->link('Documents', array('controller' => 'user_documents', 'User' => $profile['User']['id']), array('rel' => 'modal-none')); ?></li>
				<li><?php echo $this->Permission->link('Comments', array('controller' => 'comments', 'User' => $profile['User']['id']), array('rel' => 'modal-none')); ?></li>
				<li><?php echo $this->Permission->link('Edit', array('controller' => 'profiles', 'action' => 'edit', 'User' => $profile['User']['id'])); ?></li>
			</ul>

		</div>

		<div id="household">
		</div>
		<div id="payments">
		</div>
		

	</div>
</div>

<?php
$this->Js->buffer('CORE.register("households", "household", "'.Router::url(array('controller' => 'households', 'User' => $profile['User']['id'])).'");');
$this->Js->buffer('CORE.register("payments", "payments", "'.Router::url(array('controller' => 'payments', 'User' => $profile['User']['id'])).'");');
?>
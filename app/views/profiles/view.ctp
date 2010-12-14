<h1><?php echo $profile['Profile']['name']; ?></h1>

<div class="profiles core-tabs">

	<ul>
		<li><a href="#my-profile">My Profile</a></li>
		<li><?php echo $this->Html->link('My Household', array('controller' => 'households'), array('title' => 'household')); ?></li>
		<li><a href="#payments">Payments</a></li>
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
					echo '</div>';
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
					<p>
					<?php
					echo $this->Formatting->address($profile['ActiveAddress']);
					?>
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
				<div class="grid_7 alpha equal-height">
					<div id="involvement">
						<?php
						$url = Router::url(array(
							'controller' => 'rosters',
							'action' => 'involvement',
							'User' => $profile['User']['id']
						));
						$this->Js->buffer('CORE.register("involvement", "involvement", "'.$url.'")');
						$this->Js->buffer('CORE.update("involvement")');
						?>
					</div>
				</div>
				<div class="grid_3 omega equal-height">
					<div id="calendar">
						<?php
						$url = Router::url(array(
							'controller' => 'dates',
							'action' => 'calendar',
							'model' => 'User',
							'User' => $profile['User']['id']
						));
						$this->Js->buffer('CORE.register("calendar", "calendar", "'.$url.'")');
						$this->Js->buffer('CORE.update("calendar")');
						?>
					</div>
				</div>
			</div>

			<ul class="core-admin-tabs">
				<li><a href="#admin">Administration</a></li>
				<li><a href="<?php
				echo Router::url(array(
					'controller' => 'user_documents',
					'User' => $profile['User']['id']
				));
				?>" title="docs">Documents</a></li>
			</ul>

		</div>

		<div id="household">
		
		</div>
		<div id="payments">
		
		</div>
		

	</div>
</div>
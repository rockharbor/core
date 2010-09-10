<?php
/**
 * Permissions shell class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.libs.shells
 */

/**
 * Includes
 */
App::import('Core', 'Controller');
App::import('Component', 'Acl');
App::import('Model', array('DbAcl', 'Group'));

/**
 * Permissions shell.
 *
 * Shell to create/update permissions
 *
 * Allows creation of groups for CORE, as well as setting permissions. Use this shell
 * if permissions change. Use acl_extras to sync ACOs first.
 *
 * @package       core
 * @subpackage    core.app.libs.shells
 */
class PermissionsShell extends Shell {
	
/**
 * Start up and load dependent components and models
 *
 * @return void
 **/
	function startup() {
		$this->Acl =& new AclComponent();
		$controller = null;
		$this->Acl->startup($controller);
		$this->Aco =& $this->Acl->Aco;
		$this->Group =& new Group();
	}
	
/**
 * Override main() for help message hook
 *
 * @access public
 */
	function main() {
		$out  = "Available Permission commands:"."\n";
		$out .= "\t - update\n";
		$out .= "\t - create_groups\n";
		$out .= "\t - help\n\n";
		$out .= "For help, run the 'help' command.  For help on a specific command, run 'help <command>'";
		$this->out($out);
	}

/**
 * Shows help for the shell commands
 *
 * @access public
 */ 
	function help() {
		$out  = "Usage: cake permissions <command>"."\n";
		$out .= "-----------------------------------------------\n";
		
		$command = $this->args[0];
		
		if (empty($command)) {
			$out  = "Available Permission commands:"."\n";
			$out .= "\t - update\n";
			$out .= "\t - create_groups\n";
			$out .= "\t - help\n\n";
			$out .= "For help, run the 'help' command.  For help on a specific command, run 'help <command>'";
			$this->out($out);
		} else {
			switch ($command) {
				case 'update':
				$out .= "\tcake acl_extras update\n";
				$out .= "\t\tUpdates the permissions. If groups have changed, run create_groups first.\n";
				$out .= "\t\tYou should run cake acl_extras aco_sync first if controller actions have changed.\n";
				break;
				case 'create_groups':
				$out .= "\tcake acl_extras create_groups\n";
				$out .= "\t\tRemoves any existing groups and re-creates them from scratch. Run this if it\'s a fresh\n";
				$out .= "\t\tinstall or if the group table has been corrupted or compromised somehow.\n";
				break;
				default:
				$out .= "$command does not exist. Run 'help' for a list of commands.\n";
				break;
			}
			
			$this->out($out);
		}
	}

/**
 * Updates the permissions. If groups have changed, run create_groups first
 *
 * @return void
 */ 
	function update() {
		$this->Group->query('DELETE FROM `aros_acos`;');
		$this->Group->query('ALTER TABLE `aros_acos` AUTO_INCREMENT = 1');

		foreach ($this->_allowPermissions as $id => $perms) {
			$this->Group->id = $id;
			if (array_key_exists($id, $this->_denyPermissions)) {
				foreach ($this->_denyPermissions[$id] as $deny) {
					if ($this->Acl->deny($this->Group, $deny)) {
						$this->out('Permission denied for '.$id.' at '.$deny);
					} else {
						$this->out('Error: Could not set permission for '.$id.' at '.$allow);
					}					
				}
			}
			foreach ($perms as $allow) {
				if ($this->Acl->allow($this->Group, $allow)) {
					$this->out('Permission allowed for '.$id.' at '.$allow);
				} else {
					$this->out('Error: Could not set permission for '.$id.' at '.$allow);
				}
			}
		}
		
		$this->out('Complete');
	}

/**
 * Deletes and recreates groups
 *
 * @return void
 */ 
	function create_groups() {
		$this->Group->deleteAll(array('id >' => 0), false);
		$this->out('All groups deleted.');
		$this->Group->query('ALTER TABLE `groups` AUTO_INCREMENT = 1');
		$this->out('Auto increment reset to 1.');

		foreach ($this->_groups as $group) {
			$this->Group->create();
			unset($group['aro_parent']);
			if ($this->Group->save($group)) {
				$this->out($group['name'].' group created.');
			} else {
				$this->out($group['name'].' group could not be created.');
			}
		}
		
		$this->Acl->Aro->deleteAll(array('id >' => 0), false);
		$this->out('All aros deleted.');
		$this->Group->query('ALTER TABLE `aros` AUTO_INCREMENT = 1');
		$this->out('Auto increment reset to 1.');
		
		foreach ($this->_groups as $id => $arogroup) {
			$this->Acl->Aro->create();
			if ($this->Acl->Aro->save(array(
				'model' => 'Group',
				'foreign_key' => $id,
				'alias' => $arogroup['name']
			))) {
				$this->out('Aro for group '.$arogroup['name'].' created.');
			} else {
				$this->out('Error: Aro for group '.$arogroup['name'].' could not created.');
			}
		}
		$id = $arogroup = null;
		foreach ($this->_groups as $id => $arogroup) {
			if ($arogroup['aro_parent'] == null) {
				continue;
			}			
			// get parent's aro
			$aro = $this->Acl->Aro->read('id', $arogroup['aro_parent']);
		
			$this->Acl->Aro->id = $id;
			if ($this->Acl->Aro->saveField('parent_id', $aro['Aro']['id'])) {		
				$this->out('Aro for group '.$arogroup['name'].' moved under parent '.$aro['Aro']['id'].'.');
			} else {
				$this->out('Error: Aro for group '.$arogroup['name'].' could not moved.');
			}
		}
		
		$this->out('Complete');
	}
	
/**
 * Groups
 *
 * @var array
 * @access private
 */ 
	var $_groups = array(
		1 => array(
			'name' => 'Super Administrator',
			'conditional' => false,
			'parent_id' => null,
			'aro_parent' => 2
		),
		2 => array(
			'name' => 'Administrator',
			'conditional' => false,
			'parent_id' => 1,
			'aro_parent' => 3
		),
		3 => array(
			'name' => 'Pastor',
			'conditional' => false,
			'parent_id' => 2,
			'aro_parent' => 4
		),
		4 => array(
			'name' => 'Communications Admin',
			'conditional' => false,
			'parent_id' => 3,
			'aro_parent' => 5
		),
		5 => array(
			'name' => 'Staff',
			'conditional' => false,
			'parent_id' => 4,
			'aro_parent' => 6
		),
		6 => array(
			'name' => 'Intern',
			'conditional' => false,
			'parent_id' => 5,
			'aro_parent' => 7
		),
		7 => array(
			'name' => 'Developer',
			'conditional' => false,
			'parent_id' => 6,
			'aro_parent' => 8
		),
		8 => array(
			'name' => 'User',
			'conditional' => false,
			'parent_id' => 7,
			'aro_parent' => null
		),
		9 => array(
			'name' => 'Campus Manager',
			'conditional' => true,
			'parent_id' => 8,
			'aro_parent' => 10
		),
		10 => array(
			'name' => 'Ministry Manager',
			'conditional' => true,
			'parent_id' => 9,
			'aro_parent' => 11
		),
		11 => array(
			'name' => 'Involvement Leader',
			'conditional' => true,
			'parent_id' => 10,
			'aro_parent' => null
		),
		12 => array(
			'name' => 'Owner',
			'conditional' => true,
			'parent_id' => 8,
			'aro_parent' => 13
		),
		13 => array(
			'name' => 'Household Contact',
			'conditional' => true,
			'parent_id' => 12,
			'aro_parent' => null
		)
	);		
	
	
/**
 * Denied permissions
 *
 * @var array
 * @access private
 */ 
	var $_denyPermissions = array(
		/*2 => array('controllers'),
		3 => array('controllers'),
		4 => array('controllers'),
		5 => array('controllers'),
		6 => array('controllers'),
		7 => array('controllers'),
		8 => array('controllers'),
		9 => array('controllers'),
		10 => array('controllers'),
		11 => array('controllers'),
		12 => array('controllers'),
		13 => array('controllers'),
		14 => array('controllers')*/
	);
	
/**
 * Granted permissions
 *
 * @var array
 * @access private
 */ 
	var $_allowPermissions = array(		
		// super administrator
		1 => array(
			'controllers'
		),		 
		// administrator
		2 => array(
			'controllers/ApiGenerator/ApiClasses/view_source',
			'controllers/Campuses/delete',
			'controllers/Involvements/delete',
			'controllers/Logs',
			'controllers/Ministries/delete',
			'controllers/Publications',
			'controllers/Roles',
			'controllers/Rosters/delete',
			'controllers/Rosters/edit'
		),
		// communications administrator
		4 => array(
			'controllers/Alerts/add',
			'controllers/Alerts/edit',
			'controllers/Alerts/index',
			'controllers/Campuses/add',
			'controllers/Campuses/edit',
			'controllers/Campuses/revise',
			'controllers/Campuses/history',
			'controllers/Classifications/add',
			'controllers/Classifications/edit',
			'controllers/Classifications/index',
			'controllers/Classifications/view',
			'controllers/JobCategories',
			'controllers/Dates/add',
			'controllers/Dates/delete',
			'controllers/Dates/edit',
			'controllers/Dates/index',
			'controllers/Dates/view',
			'controllers/Involvements/add',
			'controllers/Involvements/edit',
			'controllers/Involvements/toggle_activity',
			'controllers/InvolvementAddresses/add',
			'controllers/InvolvementAddresses/delete',
			'controllers/InvolvementAddresses/edit',
			'controllers/InvolvementDocuments/delete',
			'controllers/InvolvementDocuments/upload',
			'controllers/InvolvementImages/delete',
			'controllers/InvolvementImages/index',
			'controllers/InvolvementImages/upload',
			'controllers/PaymentOptions',
			'controllers/Questions',
			'controllers/MergeRequests',
			'controllers/Ministries/add',
			'controllers/Ministries/edit',
			'controllers/Ministries/history',
			'controllers/Ministries/revise',
			'controllers/MinistryImages/delete',
			'controllers/MinistryImages/index',
			'controllers/MinistryImages/upload',
			'controllers/Regions',	
			'controllers/Zipcodes',
			'controllers/Schools',
			'controllers/UserImages/delete',
			'controllers/UserImages/index',
			'controllers/UserImages/upload',			
		),		
		// staff
		5 => array(			
		),
		// intern
		6 => array(
			'controllers/Comments/index',
			'controllers/Comments/add',
			'controllers/Rosters/index',
			'controllers/Rosters/add',
			'controllers/Rosters/involvement',
			'controllers/Users/add',
			'controllers/Users/edit',
			'controllers/Users/edit_profile',
			'controllers/Users/view',
			'controllers/UserDocuments/delete',
			'controllers/UserDocuments/download',
			'controllers/UserDocuments/index',
			'controllers/UserDocuments/upload',
			'controllers/UserAddresses/add',
			'controllers/UserAddresses/edit',
			'controllers/UserAddresses/index',
			'controllers/UserAddresses/delete',
			'controllers/UserImages/view',
			'controllers/Households/index',
			'controllers/Households/make_household_contact',
			'controllers/Households/shift_households',
			'controllers/Publications/subscriptions',
			'controllers/Publications/toggle_subscribe',			
			'controllers/Payments/add',
			'controllers/Payments/index',
			'controllers/Searches/user',
			'controllers/Reports/export',
			'controllers/Reports/map',
			'controllers/Reports/index',
			'controllers/Reports/ministry',
			'controllers/SysEmails/compose',
			'controllers/SysEmailDocuments',
			'controllers/Involvements/invite'
		),
		// developer
		7 => array(
			'controllers/ApiGenerator/ApiClasses/index',
			'controllers/ApiGenerator/ApiClasses/classes',
			'controllers/ApiGenerator/ApiClasses/view_class',
			'controllers/ApiGenerator/ApiClasses/search',
			'controllers/SysEmails/bug_compose'
		),		
		// user
		8 => array(
			'controllers/Alerts/history',
			'controllers/Alerts/read',
			'controllers/Alerts/view',
			'controllers/Campuses/index',
			'controllers/Campuses/view',
			'controllers/Dates/calendar',	
			'controllers/Involvements/index',
			'controllers/Involvements/view',
			'controllers/InvolvementAddresses/index',
			'controllers/InvolvementDocuments/download',
			'controllers/InvolvementDocuments/index',
			'controllers/InvolvementImages/view',
			'controllers/Ministries/index',
			'controllers/Ministries/view',
			'controllers/MinistryImages/view',
			'controllers/Notifications/delete',
			'controllers/Notifications/index',
			'controllers/Notifications/read',
			'controllers/Searches/index',
			'controllers/Searches/involvement',
			'controllers/Searches/ministry',
			'controllers/CampusLeaders/index',
			'controllers/MinistryLeaders/index',
			'controllers/InvolvementLeaders/index',
			'controllers/DebugKit',
			'controllers/CoreDebugPanels',
			'controllers/MultiSelect'
		),		
		// campus manager
		9 => array(
			'controllers/Ministries/add',
			'controllers/Campuses/edit',
			'controllers/Campuses/history',
			'controllers/CampusLeaders/add',
			'controllers/CampusLeaders/delete'
		),
		// ministry manager
		10 => array(
			'controllers/MinistryImages/delete',
			'controllers/MinistryImages/index',
			'controllers/MinistryImages/upload',
			'controllers/Involvements/add',
			'controllers/Ministries/edit',
			'controllers/Ministries/history',
			'controllers/Roles/add',
			'controllers/Roles/delete',
			'controllers/Roles/edit',
			'controllers/Roles/index',
			'controllers/Roles/view',
			'controllers/MinistryLeaders/add',
			'controllers/MinistryLeaders/delete'
		),
		// involvement leader
		11 => array(
			'controllers/Involvements/invite',
			'controllers/Involvements/invite_roster',
			'controllers/Rosters/add',
			'controllers/Rosters/delete',
			'controllers/Rosters/edit',
			'controllers/Rosters/index',
			'controllers/Users/view',
			'controllers/InvolvementLeaders/add',
			'controllers/InvolvementLeaders/delete',
			'controllers/Involvements/edit',
			'controllers/Involvements/toggle_activity',
			'controllers/InvolvementDocuments/delete',
			'controllers/InvolvementDocuments/upload',
			'controllers/Dates/add',
			'controllers/Dates/delete',
			'controllers/Dates/edit',
			'controllers/Dates/index',
			'controllers/Dates/view',
			'controllers/InvolvementImages/upload',
			'controllers/InvolvementImages/index',
			'controllers/InvolvementImages/delete',
			'controllers/InvolvementAddresses/add',
			'controllers/InvolvementAddresses/delete',
			'controllers/InvolvementAddresses/edit',
			'controllers/PaymentOptions/add',
			'controllers/PaymentOptions/delete',
			'controllers/PaymentOptions/edit',
			'controllers/PaymentOptions/index',
			'controllers/Questions/add',
			'controllers/Questions/delete',
			'controllers/Questions/edit',
			'controllers/Questions/index',
			'controllers/Questions/move',
			'controllers/Payments/add'
		),			
		// owner
		12 => array(
			'controllers/Users/edit',			
			'controllers/CampusLeaders/delete',
			'controllers/MinistryLeaders/delete',
			'controllers/InvolvementLeaders/delete'
		),		
		// household contact
		13 => array(
			'controllers/UserImages/delete',
			'controllers/UserImages/index',
			'controllers/UserImages/upload',
			'controllers/UserImages/view',
			'controllers/Households/index',
			'controllers/Households/make_household_contact',
			'controllers/Households/shift_households',
			'controllers/Rosters/add',
			'controllers/Rosters/delete',
			'controllers/Rosters/edit',
			'controllers/Rosters/involvement',
			'controllers/Users/edit_profile',
			'controllers/UserAddresses/add',
			'controllers/UserAddresses/delete',
			'controllers/UserAddresses/edit',
			'controllers/UserAddresses/index',
			'controllers/Publications/subscriptions',
			'controllers/Publications/toggle_subscribe'
		)
	);
	
}

?>
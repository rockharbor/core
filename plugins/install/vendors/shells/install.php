<?php
/**
 * Install shell class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       install
 * @subpackage    install.vendors.shells
 */

/**
 * Includes
 */
App::import('Vendor', 'Install.Records');
App::import('Core', array('Controller', 'Folder', 'ModelBehavior'));
App::import('Component', 'Acl');
App::import('Behavior', 'Tree');
App::import('Model', array('DbAcl', 'User', 'Group'));

/**
 * Install shell.
 *
 * Shell that takes care of basic database installation. Creates schema,
 * default data, admin user and ACL records.
 *
 * @package       install
 * @subpackage    install.vendors.shells
 */
class InstallShell extends Shell {
	
/**
 * Start up and load dependent components and models
 *
 * @return void
 **/
	function startup() {
		if (App::import('Shell', 'AclExtras.AclExtras') == false) {
			$this->out('ERROR: Missing acl_extras plugin.');
			$this->_stop();
		}
		if (App::import('Shell', 'Schema') == false) {
			$this->out('ERROR: Schema shell missing.');
			$this->_stop();
		}
		if (App::import('Shell', 'ApiGenerator.ApiIndex') == false) {
			$this->out('ERROR: Missing api_generator plugin.');
			$this->_stop();
		}
		
		$this->SchemaShell = new SchemaShell($this->Dispatch);
		$this->SchemaShell->startup();
		$this->_welcome();
		$this->out('CORE Install Shell');
		$this->hr();
	}
	
/**
 * Override main() for help message hook
 *
 * @access public
 */
	function main() {
		$out  = "Available Install commands:"."\n";
		$out .= "\t - install\n";
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
		$out  = "Usage: cake install <command>"."\n";
		$out .= "-----------------------------------------------\n";
		
		$command = $this->args[0];
		
		if (empty($command)) {
			$out  = "Available Install commands:"."\n";
			$out .= "\t - install\n";
			$out .= "\t - help\n\n";
			$out .= "For help, run the 'help' command.  For help on a specific command, run 'help <command>'";
			$this->out($out);
		} else {
			switch ($command) {
				case 'install':
				$out .= "\tcake install install\n";
				$out .= "\t\tInstalls the CORE database and inserts default data.\n";
				$out .= "\t\tThe default admin user is user:admin / pass:password\n";
				break;
				default:
				$out .= "$command does not exist. Run 'help' for a list of commands.\n";
				break;
			}
			
			$this->out($out);
		}
	}

/**
 * Updates permissions
 */
	function update() {
		// create Acos
		$this->Acl =& new AclComponent();
		$controller = null;
		$this->Acl->startup($controller);
		$this->Aco =& $this->Acl->Aco;
		$this->AclExtras = new AclExtrasShell($this->Dispatch);
		$this->AclExtras->startup();
		$this->AclExtras->aco_sync();

		// create aros
		$this->_createGroupAros();
		// create acl
		$this->_createAcl();
	}

/**
 * Installs the database. Generates all permissions and creates an Admin user
 *
 * @return void
 * @todo Make it sync instead of deleting everything
 */ 
	function install() {
		$response = $this->in('Install CORE database? Doing so will drop all current tables and records!', array('y', 'n'), 'n');
		if (strtolower($response) !== 'y') {
			$this->_stop();
		}

		// insert tables
		$this->SchemaShell->params['plugin'] = 'install';
		$this->SchemaShell->create();

		// insert all records
		$Folder = new Folder(APP.'plugins'.DS.'install'.DS.'config'.DS.'records');
		$files = $Folder->find();
		foreach ($files as $file) {
			if (preg_match('/[A-Za-z]_records.php/', $file)) {
				require_once APP.'plugins'.DS.'install'.DS.'config'.DS.'records'.DS.$file;
				$className = str_replace('.php', '', Inflector::camelize($file));
				$records = new $className;
				$records->insert();
				$this->out('Records added for '.str_replace('Records', '', $className));
			}
		}

		$this->update();

		// create api indices
		$ApiIndex = new ApiIndexShell($this->Dispatch);
		$ApiIndex->startup();
		$ApiIndex->update();
		
		$this->out('Complete!');
	}

/**
 * Installs or uninstalls a CORE plugin
 * 
 * ### Args:
 * - 0: the name of the plugin
 * 
 * ### Params:
 * - `-uninstall` To uninstall the plugin
 * 
 * @return void
 */
	function plugin() {
		if (isset($this->params['uninstall'])) {
			$plugin = $this->params['uninstall'];
			$this->_uninstallPlugin($plugin);
			return;
		}
		if (!isset($this->args[0])) {
			$this->out(__('Please specify a plugin to uninstall (lowercase_underscored)', true));
			$this->_stop();
		}
		$plugin = $this->args[0];
		
		if (isset($this->params['uninstall'])) {
			$this->_uninstallPlugin($plugin);
			return;
		}
		
		$response = $this->in('Install '.Inflector::humanize($plugin).' plugin?', array('y', 'n'), 'n');
		if (strtolower($response) !== 'y') {
			$this->_stop();
		}
		
		// insert tables & records
		if (file_exists(APP.'plugins'.DS.$plugin.DS.'config'.DS.'schema'.DS.$plugin.'.php')) {
			// table schema
			$this->SchemaShell->params['name'] = Inflector::camelize($plugin);
			$this->SchemaShell->params['plugin'] = $plugin;
			$this->SchemaShell->startup();
			$this->SchemaShell->create();

			// insert all records
			$Folder = new Folder(APP.'plugins'.DS.$plugin.DS.'config'.DS.'records');
			$files = $Folder->find();
			foreach ($files as $file) {
				if (preg_match('/[A-Za-z]_records.php/', $file)) {
					require_once APP.'plugins'.DS.$plugin.DS.'config'.DS.'records'.DS.$file;
					$className = str_replace('.php', '', Inflector::camelize($file));
					$records = new $className;
					$records->insert();
					$this->out('Records added for '.str_replace('Records', '', $className));
				}
			}
		}
		
		// sync acos
		$this->AclExtras = new AclExtrasShell($this->Dispatch);
		$this->AclExtras->startup();
		$this->AclExtras->aco_sync();
		
		// add the app setting
		$this->out(__('Registering plugin', true));
		$AppSetting = ClassRegistry::init('AppSetting');
		$AppSetting->save(array(
			'name' => 'plugin.'.$plugin,
			'description' => Inflector::humanize($plugin).' Plugin',
			'type' => 'plugin'
		));
		
		// run the plugin's install file
		$class = Inflector::camelize($plugin).'Install';
		if (App::import('Plugin', $class)) {
			$this->out(__('Running plugin\'s install routine', true));
			$installer = new $class;
			$installer->install();
		}
	}
	
/**
 * Uninstalls a plugin, removing it's tables and setting
 * 
 * @param string $plugin 
 */
	function _uninstallPlugin($plugin) {
		$response = $this->in('Uninstall '.Inflector::humanize($plugin).' plugin? All tables associated with this plugin will be dropped!', array('y', 'n'), 'n');
		if (strtolower($response) !== 'y') {
			$this->_stop();
		}
		
		if (file_exists(APP.'plugins'.DS.$plugin.DS.'config'.DS.'schema'.DS.$plugin.'.php')) {
			$this->SchemaShell->params['name'] = Inflector::camelize($plugin);
			$this->SchemaShell->params['plugin'] = $plugin;
			$this->SchemaShell->startup();
			
			list($Schema, $table) = $this->SchemaShell->_loadSchema();
			$db =& ConnectionManager::getDataSource($this->SchemaShell->Schema->connection);
			
			$drop = array();
			foreach ($Schema->tables as $table => $fields) {
				$drop[$table] = $db->dropSchema($Schema, $table);
			}
			
			if ($this->in(__('Are you sure you want to drop the table(s)?', true), array('y', 'n'), 'n') == 'y') {
				$this->out(__('Dropping table(s).', true));
				$this->SchemaShell->__run($drop, 'drop', $Schema);
			}
		}
		
		// remove from app settings
		$this->out(__('Unregistering plugin', true));
		$AppSetting = ClassRegistry::init('AppSetting');
		$result = $AppSetting->findByName('plugin.'.$plugin);
		$AppSetting->delete($result['AppSetting']['id']);
		
		// remove acos for this plugin
		$this->out(__('Removing ACOs', true));
		Core::removeAco(Inflector::humanize($plugin));
		
		// run the plugin's uninstall file
		$class = Inflector::camelize($plugin).'Install';
		if (App::import('Plugin', $class)) {
			$this->out(__('Running plugin\'s uninstall routine', true));
			$installer = new $class;
			$installer->uninstall();
		}
	}

	function _createAcl() {
		$Group = ClassRegistry::init('Group');

		$this->Acl->Aro->query('DELETE FROM aros_acos');
		$this->Acl->Aro->query('ALTER TABLE aros_acos AUTO_INCREMENT = 1');

		foreach ($this->_allowPermissions as $alias => $perms) {
			$group = $Group->findByName($alias);
			$Group->id = $group['Group']['id'];
			
			foreach ($perms as $allow) {
				if (@$this->Acl->allow($Group, $allow)) {
					$this->out('Permission allowed for '.$alias.' at '.$allow);
				} else {
					$this->out('Error: Could not set permission for '.$alias.' at '.$allow);
				}
			}
		}
	}

	function _createTable($schema, $table, $fields) {
		$db =& ConnectionManager::getDataSource('default');
		$schema->_build(array($table => $fields));
		$db->execute($db->dropSchema($schema), array('log' => false));

		$schema->_build(array($table => $fields));
		$db->execute($db->createSchema($schema), array('log' => false));
	}

/**
 * Creates aros for groups
 *
 * @return void
 * @todo make it sync so adding groups is easier and doesn't affect app
 */ 
	function _createGroupAros() {
		$Group = ClassRegistry::init('Group');
		$Group->Behaviors->attach('Tree');
		$groups = $Group->find('all', array(
			'order' => 'lft DESC'
		));

		$this->Acl->Aro->deleteAll(array('id >' => 0));
		$this->Acl->Aro->query('ALTER TABLE aros AUTO_INCREMENT = 1');

		foreach ($groups as $group) {
			$child = $Group->children($group['Group']['id'], true);
			$parent = null;
			if (count($child) > 0 && $group['Group']['conditional'] == $child[0]['Group']['conditional']) {
				$parentAro = $this->Acl->Aro->findByAlias($child[0]['Group']['name']);
				$parent = $parentAro['Aro']['id'];
			}
			$this->Acl->Aro->create();
			if ($this->Acl->Aro->save(array(
				'model' => 'Group',
				'foreign_key' => $group['Group']['id'],
				'alias' => $group['Group']['name'],
				'parent_id' => $parent
			))) {
				$this->out('Aro for group '.$group['Group']['name'].' created.');
			} else {
				$this->out('Error: Aro for group '.$group['Group']['name'].' could not created.');
			}
		}
	}
	
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
		'Super Administrator' => array(
			'controllers'
		),
		'Administrator' => array(
			'controllers/ApiGenerator/ApiClasses/view_source',
			'controllers/Campuses/delete',
			'controllers/Involvements/delete',
			'controllers/Logs',
			'controllers/Ministries/delete',
			'controllers/Publications',
			'controllers/Rosters/delete',
			'controllers/Rosters/edit',
			'controllers/Profiles/admin',
			'controllers/MinistryLeaders/add',
			'controllers/PaymentTypes',
			'controllers/InvolvementTypes'
		),
		'Communications Admin' => array(
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
			'controllers/Involvements/add',
			'controllers/Involvements/edit',
			'controllers/Involvements/toggle_activity',
			'controllers/Involvements/invite',
			'controllers/Involvements/invite_roster',
			'controllers/InvolvementAddresses/add',
			'controllers/InvolvementAddresses/delete',
			'controllers/InvolvementAddresses/edit',
			'controllers/InvolvementAddresses/primary',
			'controllers/InvolvementAddresses/toggle_activity',
			'controllers/InvolvementDocuments/delete',
			'controllers/InvolvementImages/delete',
			'controllers/InvolvementImages/index',
			'controllers/InvolvementImages/approve',
			'controllers/InvolvementImages/promote',
			'controllers/InvolvementLeaders/add',
			'controllers/InvolvementLeaders/delete',
			'controllers/PaymentOptions',
			'controllers/Questions',
			'controllers/MergeRequests',
			'controllers/Ministries/add',
			'controllers/Ministries/edit',
			'controllers/Ministries/history',
			'controllers/Ministries/revise',
			'controllers/Ministries/bulk_edit',
			'controllers/MinistryImages/delete',
			'controllers/MinistryImages/index',
			'controllers/MinistryImages/approve',
			'controllers/MinistryImages/promote',
			'controllers/Regions',	
			'controllers/Zipcodes',
			'controllers/Schools',
			'controllers/UserImages/delete',
			'controllers/UserImages/approval',
			'controllers/UserImages/upload',
			'controllers/UserImages/approve',
			'controllers/Roles',
			'controllers/RosterStatuses',
			'controllers/Images/approval',
		),
		'Intern' => array(
			'controllers/Comments/index',
			'controllers/Comments/add',
			'controllers/Rosters/index',
			'controllers/Rosters/add',
			'controllers/Rosters/roles',
			'controllers/Rosters/involvement',
			'controllers/Users/add',
			'controllers/Users/edit',
			'controllers/UserDocuments/delete',
			'controllers/UserDocuments/download',
			'controllers/UserDocuments/index',
			'controllers/UserDocuments/upload',
			'controllers/UserDocuments/approve',
			'controllers/UserAddresses/add',
			'controllers/UserAddresses/edit',
			'controllers/UserAddresses/index',
			'controllers/UserAddresses/delete',
			'controllers/UserImages/view',
			'controllers/Households/index',
			'controllers/Households/confirm',
			'controllers/Households/make_household_contact',
			'controllers/Households/shift_households',
			'controllers/Publications/subscriptions',
			'controllers/Publications/toggle_subscribe',			
			'controllers/Payments/add',
			'controllers/Payments/index',
			'controllers/Searches/user',
			'controllers/Reports/map',
			'controllers/Reports/index',
			'controllers/Reports/payments',
			'controllers/SysEmails/compose',
			'controllers/SysEmailDocuments',
			'controllers/Involvements/invite',
			'controllers/MinistryImages/upload',
			'controllers/InvolvementDocuments/upload',
			'controllers/InvolvementDocuments/approve',
			'controllers/InvolvementImages/upload',
			'controllers/Profiles/view',
			'controllers/Profiles/edit',
			'controllers/Payments/view',
			'controllers/Users/dashboard'
		),
		'Developer' => array(
			'controllers/ApiGenerator/ApiClasses/index',
			'controllers/ApiGenerator/ApiClasses/classes',
			'controllers/ApiGenerator/ApiClasses/view_class',
			'controllers/ApiGenerator/ApiClasses/search',
			'controllers/SysEmails/bug_compose'
		),
		'User' => array(
			'controllers/Alerts/history',
			'controllers/Alerts/read',
			'controllers/Alerts/view',
			'controllers/Campuses/index',
			'controllers/Campuses/view',
			'controllers/Dates/calendar',
			'controllers/Dates/index',
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
			'controllers/Notifications/quick',
			'controllers/Notifications/read',
			'controllers/Searches/index',
			'controllers/Searches/involvement',
			'controllers/Searches/ministry',
			'controllers/Searches/simple',
			'controllers/CampusLeaders/index',
			'controllers/MinistryLeaders/index',
			'controllers/InvolvementLeaders/index',
			'controllers/DebugKit',
			'controllers/CoreDebugPanels',
			'controllers/MultiSelect',
			'controllers/Reports/export',
			'controllers/Reports/map',
			'controllers/InvolvementLeaders/dashboard',
			'controllers/MinistryLeaders/dashboard',
			'controllers/Users/household_add',
		),
		'Campus Manager' => array(
			'controllers/Ministries/add',
			'controllers/Campuses/edit',
			'controllers/Campuses/history',
			'controllers/CampusLeaders/add',
			'controllers/CampusLeaders/delete'
		),
		'Ministry Manager' => array(
			'controllers/MinistryImages/delete',
			'controllers/MinistryImages/index',
			'controllers/MinistryImages/upload',
			'controllers/Involvements/add',
			'controllers/Ministries/edit',
			'controllers/Ministries/history',
			'controllers/MinistryLeaders/add',
			'controllers/MinistryLeaders/delete',
			'controllers/Ministries/bulk_edit',
			'controllers/Roles'
		),
		'Involvement Leader' => array(
			'controllers/Involvements/invite',
			'controllers/Involvements/invite_roster',
			'controllers/Rosters/add',
			'controllers/Rosters/delete',
			'controllers/Rosters/edit',
			'controllers/Rosters/index',
			'controllers/InvolvementLeaders/add',
			'controllers/InvolvementLeaders/delete',
			'controllers/Involvements/edit',
			'controllers/Involvements/toggle_activity',
			'controllers/InvolvementDocuments/delete',
			'controllers/InvolvementDocuments/upload',
			'controllers/Dates/add',
			'controllers/Dates/delete',
			'controllers/Dates/edit',
			'controllers/InvolvementImages/upload',
			'controllers/InvolvementImages/index',
			'controllers/InvolvementImages/delete',
			'controllers/InvolvementAddresses/add',
			'controllers/InvolvementAddresses/delete',
			'controllers/InvolvementAddresses/edit',
			'controllers/InvolvementAddresses/primary',
			'controllers/InvolvementAddresses/toggle_activity',
			'controllers/PaymentOptions/add',
			'controllers/PaymentOptions/delete',
			'controllers/PaymentOptions/edit',
			'controllers/PaymentOptions/index',
			'controllers/Questions/add',
			'controllers/Questions/delete',
			'controllers/Questions/edit',
			'controllers/Questions/index',
			'controllers/Questions/move',
			'controllers/Payments/add',
			'controllers/Rosters/confirm',
			'controllers/Rosters/roles'
		),
		'Owner' => array(
			'controllers/Users/edit',			
			'controllers/CampusLeaders/delete',
			'controllers/MinistryLeaders/delete',
			'controllers/InvolvementLeaders/delete',
			'controllers/Profiles/view',
			'controllers/Households/confirm',
			'controllers/Profiles/edit',
			'controllers/Payments/view',
			'controllers/Payments/index'
		),
		'Household Contact' => array(
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
			'controllers/Profiles/view',
			'controllers/UserAddresses/add',
			'controllers/UserAddresses/edit',
			'controllers/UserAddresses/index',
			'controllers/UserAddresses/primary',
			'controllers/UserAddresses/toggle_activity',
			'controllers/Publications/subscriptions',
			'controllers/Publications/toggle_subscribe',
			'controllers/Profiles/edit',
			'controllers/Payments/view',
			'controllers/Payments/index'
		)
	);
	
}

?>
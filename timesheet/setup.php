<?php
/*  Timesheet module for DotProject
    Copyright (C) 2008  SEA Tecnologia

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php
/*
 * Name:      Timesheet
 * Directory: timesheet
 * Version:   1.0.0
 * Type:      user
 * UI Name:   Timesheet
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Timesheet';		// name the module
$config['mod_version'] = '1.0.0';		// add a version number
$config['mod_directory'] = 'timesheet';		// tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupTimesheet';	// the name of the PHP setup class (used below)
$config['mod_type'] = 'user';			// 'core' for modules distributed with dP by standard, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Timesheet';		// the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'communicate.gif';	// name of a related icon
$config['mod_description'] = 'Tabela de cadastro de horas trabalhadas.';	// some description of the module
$config['mod_config'] = true;			// show 'configure' link in viewmods

// show module configuration with the dPframework (if requested via http)
if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupTimesheet {

	function configure() {		// configure this module
		global $AppUI;
		$AppUI->redirect( 'm=timesheet&a=configure' );	// load module specific configuration page
  		return true;
	}

	function remove() {		// run this method on uninstall process
		db_exec( "DROP TABLE dot_calendar_config;" );	// remove the timesheet table from database
		db_exec( "DROP TABLE google_account;" );
		return null;
	}


	function upgrade( $old_version ) {	// use this to provide upgrade functionality between different versions; not relevant here

		switch ( $old_version )
		{
		case "all":		// upgrade from scratch (called from install)
		case "0.9":
			//do some alter table commands

		case "1.0":
			return true;

		default:
			return false;
		}

		return false;
	}

	function install() {
		$sql1 = "CREATE TABLE dot_calendar_config ( ".
			" config_id int(11) unsigned NOT NULL auto_increment".
			", my_crypt_key text NOT NULL".
			", my_crypt_iv text NOT NULL".
			", task_sync_interval int(10) NOT NULL".
			", task_log_sync_interval int(10) NOT NULL".
			", PRIMARY KEY (config_id)" .
			", UNIQUE KEY config_id (config_id)" .
			" ) TYPE=MyISAM;";
		db_exec( $sql1 ); db_error();

		$sql2 = "CREATE TABLE google_account ( ".
			" google_account_id int(11) unsigned NOT NULL auto_increment".
			", user_id int(10) NOT NULL".
			", google_account_password text NOT NULL".
			", google_account_email text NOT NULL".
			", PRIMARY KEY (google_account_id)" .
			", UNIQUE KEY google_account_id (google_account_id)" .
			" ) TYPE=MyISAM;";
		db_exec( $sql2 ); db_error();
		return null;
	}

}

?>

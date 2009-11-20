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
// use the dPFramework to have easy database operations (store, etc.) by using its ObjectOrientedDesign
// therefore we have to create a child class for the module einstein

// a class named (like this) in the form: module/module.class.php is automatically loaded by the dPFramework

/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision: 1.0 $
*/

// include the powerful parent class that we want to extend for einstein
require_once( $AppUI->getSystemClass ('dp' ) );		// use the dPFramework for easy inclusion of this class here

/**
 * The Timesheet Class
 */
class CTimesheet extends CDpObject {
	// link variables to the einstein object (according to the existing columns in the database table einstein)
	var $task_log_id = NULL;//use NULL for a NEW object, so the database automatically assigns an unique id by 'NOT NULL'-functionality
	var $task_log_name = NULL;
	var $task_log_task  = NULL;                                             
	var $task_log_description = NULL;
	var $task_log_creator = NULL;
	var $task_log_hours = NULL;
	var $task_log_date = NULL;
	
	//valor default pois não são alterados na tela de log do timesheet
	var $task_log_costcode = NULL;
	var $task_log_problem = NULL;
	var $task_log_reference = NULL;
	var $task_log_related_url = NULL; 
	// the constructor of the CTimesheet class, always combined with the table name and the unique key of the table
	function CTimesheet() {
		$this->CDpObject( 'task_log', 'task_log_id' );
	}
}
?>

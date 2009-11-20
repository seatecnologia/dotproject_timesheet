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
// this doSQL script is called from the addedit.php script
// its purpose is to use the CTimesheet class to interoperate with the database (store, edit)

/* the following variables can be retreived via POST from timesheet/addedit.php:
** int task_log_id	is '0' if a new database object has to be stored or the id of an existing log that should be overwritten or deleted in the db
** str task_log_name	the text of the quote that should be stored
*/

// create a new instance of the einstein class
$obj = new CTimesheet();
$msg = '';	// reset the message string

// bind the informations (variables) retrieved via post to the einstein object
if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
if($_POST['progresso']){
	$id = $obj->task_log_task;
	$sql2 = "UPDATE tasks set task_percent_complete = 100 WHERE task_id=".$id;	
	db_exec( $sql2 );
}
if ($obj->task_log_task) {
	$id = $obj->task_log_task;
	$sql = "SELECT task_name FROM tasks WHERE task_id=".$id;
	$task = db_loadList( $sql );
	foreach ($task as $row) {
		$task_log_name = $row['task_name'];
	}
	$obj->task_log_name = $task_log_name;
}
	// simply store the added/edited log in database via the store method of the einstein child class of the CDpObject provided ba the dPFramework
	// no sql command is necessary here! :-)
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['task_log_id'];
		$AppUI->setMsg( $isNotNew ? 'Log task updated' : 'Log task inserted', UI_MSG_OK);
	}
	$AppUI->redirect();
?>

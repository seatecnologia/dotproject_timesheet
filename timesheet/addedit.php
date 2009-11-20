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
// one site for both adding and editing timesheet's log items
// besides the following lines show the possiblities of the dPframework

// retrieve GET-Parameters via dPframework
// please always use this way instead of hard code (e.g. there have been some problems with REGISTER_GLOBALS=OFF with hard code)
GLOBAL $AppUI, $user_id, $percent;
$user_id    = $AppUI->user_id;
$task_log_id = intval( dPgetParam( $_GET, "task_log_id", 0 ) );
$task_log_name = intval( dPgetParam( $_GET, "task_log_name", 0 ) );

// check permissions for this record
$canEdit = !getDenyEdit( $m, $task_log_id );
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// use the object oriented design of dP for loading the log that should be edited
// therefore create a new instance of the Timesheet Class
$obj = new CTimesheet();
$df = $AppUI->getPref( 'SHDATEFORMAT' );

// pull users
// pull users
$q  = new DBQuery;
$q->addTable('tasks','t');
$q->addTable('projects','p');
$q->addTable('user_tasks','u');
$q->addQuery('t.task_id');
$q->addQuery('CONCAT_WS(" - ",p.project_short_name, t.task_name)');
$q->addOrder('p.project_short_name, t.task_name');
$q->addWhere('t.task_project = p.project_id and t.task_dynamic = 0 and t.task_percent_complete!=100 and u.task_id=t.task_id and u.user_id='.$user_id);
//Devido a possibilidade de edição de registros, as tarefas de projetos arquivados e em espera serão apresentadas.
//$q->addWhere('p.project_status!=7 and p.project_status!=4');//[7] Projetos Arquivados e [4] Projetos Em Espera
$tasks = $q->loadHashList();

// load the record data in case of that this script is used to edit the log qith task_log_id (transmitted via GET)
if (!$obj->load( $task_log_id, false ) && $task_log_id > 0) {
	// show some error messages using the dPFramework if loadOperation failed
	// these error messages are nicely integrated with the frontend of dP
	// use detailed error messages as often as possible
	$AppUI->setMsg( 'Timesheet' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();					// go back to the calling location
}

// check if this record has dependancies to prevent deletion
$msg = '';

// setup the title block
// Fill the title block either with 'Edit' or with 'New' depending on if task_log_id has been transmitted via GET or is empty
$ttl = $task_log_id > 0 ? "Edit Log" : "New Log";
$titleBlock = new CTitleBlock( $ttl, 'timesheet.png', $m, "$m.$a" );
// also have a breadcrumb here
// breadcrumbs facilitate the navigation within dP as they did for haensel and gretel in the identically named fairytale
$titleBlock->addCrumb( "?m=timesheet", "timesheet home" );
$titleBlock->show();
?>
<script language="JavaScript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
      idate = eval( 'document.editFrm.task_' + field + '.value' );
  //      idate = eval( 'document.editFrm.' + field + '2.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.task_' + calendarField );
//	fld_date = eval( 'document.editFrm.' + calendarField + '2' );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}
</script>
<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">
<form name="editFrm" action="./index.php?m=timesheet" method="post">
	<input type="hidden" name="dosql" value="do_log_aed" />
	<input type="hidden" name="task_log_id" value="<?php echo $task_log_id;?>" />
	<input type="hidden" name="task_log_name" value="<?php echo $task_log_name;?>" />
	<input type="hidden" name="task_log_creator" value="<?php echo $user_id;?>" />
  <tr>
    <th nowrap="nowrap"><?php echo $AppUI->_( 'Date' );
	?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Task' );
	?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Description' );
	?></th>
    	<th nowrap="nowrap"><?php echo $AppUI->_('Progress');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Hours Worked' );?></th>
	<th nowrap="nowrap">&nbsp;</th>
  </tr>
  <tr valign="top">
	<?php $log_date = new CDate( $obj->task_log_date );?>
    <td>      
	<input type="hidden" name="task_log_date" value="<?php echo $log_date->format( FMT_DATETIME_MYSQL );?>">
      <input type="text" name="log_date" size="10" value="<?php echo $log_date->format( $df );?>" class="text" disabled="disabled">
      <a href="#" onClick="popCalendar('log_date')">
	<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
      </a>
    </td>
    <td>
	<?php
	echo arraySelect( $tasks, 'task_log_task', 'size="1" class="text" onchange="javascript:task_log_task.value = this.options[this.selectedIndex].value;"', $obj->task_log_task );
	?>
    </td>
		
    <td>
	<textarea name="task_log_description" class="textarea" cols="50" rows="6"><?php echo $obj->task_log_description;?></textarea>
    </td>
     <td><b><?php echo $AppUI->_('End Task?');?> </b><input type=checkbox name="progresso"></td>
    <td>
	<input name="task_log_hours" type="text" id="task_log_hours" value="<?php echo dPformSafe( $obj->task_log_hours );?>" size="10" width="60" />
    </td>
    <td>
	<input class="button" type="submit" name="btnFuseAction" value="<?php echo $AppUI->_('update task');?>"/>
     </td>
<tr>
</form>
</table>

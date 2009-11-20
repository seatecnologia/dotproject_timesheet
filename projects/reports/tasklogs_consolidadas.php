<?php /* PROJECTS $Id: tasklogs.php,v 1.12.10.9 2007/05/17 15:24:23 caseydk Exp $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
* Generates a report of the task logs for given dates
*/
$perms =& $AppUI->acl();
if (! $perms->checkModule('tasks', 'view')) {
	redirect('m=public&a=access_denied');
}	
$do_report = dPgetParam( $_GET, "do_report", 0 );
$log_all = dPgetParam( $_GET, 'log_all', 0 );
$log_pdf = dPgetParam( $_GET, 'log_pdf', 0 );
$log_ignore = dPgetParam( $_GET, 'log_ignore', 0 );
$log_userfilter = dPgetParam( $_GET, 'log_userfilter', '0' );

$log_start_date = dPgetParam( $_GET, "log_start_date", 0 );
$log_end_date = dPgetParam( $_GET, "log_end_date", 0 );

// create Date objects from the datetime fields
$start_date = intval( $log_start_date ) ? new CDate( $log_start_date ) : new CDate();
$end_date = intval( $log_end_date ) ? new CDate( $log_end_date ) : new CDate();

if (!$log_start_date) {
	$start_date->subtractSpan( new Date_Span( "14,0,0,0" ) );
}
$end_date->setTime( 23, 59, 59 );

?>
<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=270, scrollbars=no' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.log_' + calendarField );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}
</script>

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">

<form name="editFrm" action="" method="GET">
<input type="hidden" name="m" value="projects" />
<input type="hidden" name="a" value="reports" />
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_start_date" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format( $df );?>" class="text" disabled="disabled" style="width: 80px" />
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to');?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" style="width: 80px"/>
		<a href="#" onClick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>

	<TD NOWRAP>
		<?php echo $AppUI->_('User');?>:
		<SELECT NAME="log_userfilter" CLASS="text" STYLE="width: 80px">

	<?php
		$usersql = "
		SELECT user_id, user_username, contact_first_name, contact_last_name
		FROM users
                LEFT JOIN contacts ON user_contact = contact_id ORDER BY user_username
		";

		if ( $log_userfilter == 0 ) {
			echo '<OPTION VALUE="0" SELECTED>'.$AppUI->_('All users' );
		} else {
			echo '<OPTION VALUE="0">All users';
		}

		if (($rows = db_loadList( $usersql, NULL ))) {
			foreach ($rows as $row) {
				if ( $log_userfilter == $row["user_id"])
					echo "<OPTION VALUE='".$row["user_id"]."' SELECTED>".$row["user_username"];
				else
					echo "<OPTION VALUE='".$row["user_id"]."'>".$row["user_username"];
			}
		}

	?>

		</SELECT>
	</TD>

	<td nowrap="nowrap">
		<input type="checkbox" name="log_all" <?php if ($log_all) echo "checked" ?> />
		<?php echo $AppUI->_( 'Log All' );?>
	</td>

	<td align="right" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>
</form>
</table>

<?php
if ($do_report) {

	$sql = "SELECT p.project_id, p.project_name, t.*, CONCAT_WS(' ',contact_first_name,contact_last_name) AS creator"
		."\nFROM task_log AS t"
		."\nLEFT JOIN users AS u ON user_id = task_log_creator"
                ."\nLEFT JOIN contacts ON user_contact = contact_id, tasks"
		."\nLEFT JOIN projects p ON p.project_id = task_project"
		."\nWHERE task_log_task = task_id";
	if ($project_id != 0) {
		$sql .= "\nAND task_project = $project_id";
	}
	
	if (!$log_all) {
		$sql .= "\n	AND task_log_date >= '".$start_date->format( FMT_DATETIME_MYSQL )."'"
		."\n	AND task_log_date <= '".$end_date->format( FMT_DATETIME_MYSQL )."'";
	}
	if ($log_ignore) {
		$sql .= "\n	AND task_log_hours > 0";
	}
	if ($log_userfilter) {
		$sql .= "\n	AND task_log_creator = $log_userfilter";
	}

	$proj =& new CProject;
	$allowedProjects = $proj->getAllowedSQL($AppUI->user_id, 'task_project');
	if (count($allowedProjects)) {
		$sql .= "\n     AND " . implode(" AND ", $allowedProjects);
	}

	$sql .= " ORDER BY task_log_date";

	//echo "<pre>$sql</pre>";

	$logs = db_loadList( $sql );
	echo db_error();
	
	$hours = 0;
	$user = "" ;
        foreach ($logs as $log) {
 		$user = $log['creator'];
		$hours = $log['task_log_hours'];
		//printf("%s cadastrou %.2f horas<br/>", $user, $hours);
		
		$searences[$user] += $hours;

		#print_r(array_keys($totais));
		#echo "<br/>";

	}

	echo "<p/>";
	$nomes = array_keys($searences);
 	asort($nomes, SORT_STRING);
	foreach ($nomes as $nome) {
		printf("%s, %.2f<br/>", $nome, $searences[$nome]);
	}

}
?>

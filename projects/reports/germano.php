<?php

define("DP_BASE_DIR","BAHH");

echo "oi" ;
require_once '/var/www/netuno/classes/date.class.php';
echo "oi" ;

$log_start_date = $_GET["log_start_date"];
$log_end_date = $_GET["log_end_date"];
$start_date = intval( $log_start_date ) ? new CDate( $log_start_date ) : new CDate();
$end_date = intval( $log_end_date ) ? new CDate( $log_end_date ) : new CDate();


	$sql = "SELECT p.project_id, p.project_name, t.*, CONCAT_WS(' ',contact_first_name,contact_last_name) AS creator"
		."\nFROM task_log AS t"
		."\nLEFT JOIN users AS u ON user_id = task_log_creator"
                ."\nLEFT JOIN contacts ON user_contact = contact_id, tasks"
		."\nLEFT JOIN projects p ON p.project_id = task_project"
		."\nWHERE task_log_task = task_id";
	
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

	echo $sql;

	$proj =& new CProject;
	$allowedProjects = $proj->getAllowedSQL($AppUI->user_id, 'task_project');
	if (count($allowedProjects)) {
		$sql .= "\n     AND " . implode(" AND ", $allowedProjects);
	}

	$sql .= " ORDER BY task_log_date";

	//echo "<pre>$sql</pre>";

	$logs = db_loadList( $sql );
	echo db_error();
	
	$hours = 0.0;
	$pdfdata = array();

        foreach ($logs as $log) {
		$date = new CDate( $log['task_log_date'] );
		$hours += $log['task_log_hours'];

		$pdfdata[] = array(
			$log['creator'],
			$log['task_log_name'],
			$log['task_log_description'],
			$date->format( $df ),
			sprintf( "%.2f", $log['task_log_hours'] ),
			$log['task_log_costcode'],
		);
		echo $date->format( $df );
		printf( "%.2f", $log['task_log_hours'] );
	}
	$pdfdata[] = array(
		'',
		'',
		'',
		$AppUI->_('Total Hours').':',
		sprintf( "%.2f", $hours ),
		'',
	);
	
        echo " Total de Horas" ;	
        printf( "%.2f", $hours );
?>

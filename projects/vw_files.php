<?php /* PROJECTS $Id: vw_files.php,v 1.8.12.2 2007/03/06 00:34:42 merlinyoda Exp $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig;

$showProject = false;
require( DP_BASE_DIR.'/modules/files/index_table.php' );
?>

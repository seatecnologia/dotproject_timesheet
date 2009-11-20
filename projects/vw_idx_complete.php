<?php /* PROJECTS $Id: vw_idx_complete.php,v 1.11.6.6 2007/04/07 11:45:52 cyberhorse Exp $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $projects, $company_id;
$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');
?>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td align="right" width="65" nowrap="nowrap">&nbsp;<?php echo $AppUI->_('sort by');?>:&nbsp;</td>
</tr>
<tr>
        <th nowrap="nowrap">
                <a href="?m=projects&orderby=project_color_identifier" class="hdr"><?php echo $AppUI->_('Color');?></a>
        </th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=company_name" class="hdr"><?php echo $AppUI->_('Company');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_name" class="hdr"><?php echo $AppUI->_('Project Name');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=user_username" class="hdr"><?php echo $AppUI->_('Owner');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=total_tasks" class="hdr"><?php echo $AppUI->_('Tasks');?></a>
		<a href="?m=projects&orderby=my_tasks class="hdr">(<?php echo $AppUI->_('My');?>)</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_end_date" class="hdr"><?php echo $AppUI->_('Due Date');?>:</a>
	</th>
</tr>

<?php
$CR = "\n";
$CT = "\n\t";
$none = true;
foreach ($projects as $row) {
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id']))
		continue;
	if ($row["project_status"] == 5) {
		$none = false;
		$end_date = intval( @$row["project_end_date"] ) ? new CDate( $row["project_end_date"] ) : null;

		$s = '<tr>';
		$s .= '<td width="65" align="center" style="border: outset #eeeeee 2px;background-color:#'
			. $row["project_color_identifier"] . '">';
		$s .= $CT . '<font color="' . bestColor( $row["project_color_identifier"] ) . '">'
			. sprintf( "%.1f%%", $row["project_percent_complete"] )
			. '</font>';
		$s .= $CR . '</td>';

		$s .= $CR . '<td width="30%">';
		if ($perms->checkModuleItem('companies', 'access', $row['project_company'])) {
			$s .= $CT . '<a href="?m=companies&a=view&company_id=' . $row["project_company"] . '" title="' . htmlspecialchars( $row["company_description"], ENT_QUOTES ) . '">' . htmlspecialchars( $row["company_name"], ENT_QUOTES ) . '</a>';
		} else {
			$s .= $CT . htmlspecialchars( $row["company_name"], ENT_QUOTES );
		}
		$s .= $CR . '</td>';

		$s .= $CR . '<td width="100%">';
		$s .= $CT . '<a href="?m=projects&a=view&project_id=' . $row["project_id"] . '" onmouseover="return overlib( \''.htmlspecialchars( '<div><p>'.str_replace(array("\r\n", "\n", "\r"), '</p><p>', addslashes($row["project_description"])).'</p></div>', ENT_QUOTES ).'\', STICKY, CAPTION, \''.$AppUI->_('Description').'\', CENTER);" onmouseout="nd();">' . htmlspecialchars( $row["project_name"], ENT_QUOTES ) . '</a>';
		$s .= $CR . '</td>';
		$s .= $CR . '<td nowrap="nowrap">' . htmlspecialchars( $row["user_username"], ENT_QUOTES ) . '</td>';
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . $row["total_tasks"] . ($row["my_tasks"] ? ' ('.$row["my_tasks"].')' : '');
		$s .= $CR . '</td>';
		$s .= $CR . '<td align="right" nowrap="nowrap">';
		$s .= $CT . ($end_date ? $end_date->format( $df ) : '-');
		$s .= $CR . '</td>';
		$s .= $CR . '</tr>';
		echo $s;
	}
}
if ($none) {
	echo $CR . '<tr><td colspan="6">' . $AppUI->_( 'No projects available' ) . '</td></tr>';
}
?>
<tr>
	<td colspan="6">&nbsp;</td>
</tr>
</table>

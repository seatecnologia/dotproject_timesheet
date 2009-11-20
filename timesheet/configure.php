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
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
$sql = "SELECT * FROM dot_calendar_config";
$registro = db_loadList( $sql );
foreach ($registro as $row) {
	$config_id = $row["config_id"];
	$my_crypt_key = $row["my_crypt_key"];
	$my_crypt_iv = $row["my_crypt_iv"];
	$task_sync_interval = $row["task_sync_interval"];
	$task_log_sync_interval = $row["task_log_sync_interval"];
}
// deny all but system admins
$canEdit = !getDenyEdit( 'system' );
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$AppUI->savePlace();
$q  = new DBQuery;

if ($config_id>0 && isset( $_POST['forcesubmit'] )) {
	$q->addTable('dot_calendar_config');
	$q->addUpdate('my_crypt_key', $_POST['my_crypt_key']);
	$q->addUpdate('my_crypt_iv', $_POST['my_crypt_iv']);
	$q->addUpdate('task_sync_interval', $_POST['task_sync_interval']);
	$q->addUpdate('task_log_sync_interval', $_POST['task_log_sync_interval']);
	$q->addWhere('config_id', $config_id);
	if (!$q->exec()) {
		$AppUI->setMsg( db_error(), UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "Configurado", UI_MSG_OK );
	}
	$q->clear();
	$AppUI->redirect( 'm=timesheet&a=configure' );
} else if (isset( $_POST['forcesubmit'] ) && !$config_id>0){
	$q->addTable('dot_calendar_config');
	$q->addInsert('my_crypt_key', $_POST['my_crypt_key']);
	$q->addInsert('my_crypt_iv', $_POST['my_crypt_iv']);
	$q->addInsert('task_sync_interval', $_POST['task_sync_interval']);
	$q->addInsert('task_log_sync_interval', $_POST['task_log_sync_interval']);
	if (!$q->exec()) {
		$AppUI->setMsg( db_error(), UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "Configurado", UI_MSG_OK );
	}
	$q->clear();
	$AppUI->redirect( 'm=timesheet&a=configure');
}

// setup the title block
$titleBlock = new CTitleBlock( $AppUI->_('Configure Google Account'), 'timesheet.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=system", 'administração do sistema' );
$titleBlock->addCrumb( "?m=system&a=viewmods", 'lista de módulos' );
$titleBlock->show();

?>

<script language="javascript">
function submitFrm( frmName ) {

	eval('document.'+frmName+'.submit();');

}
</script>
<form name="frmTimeSheet" method="post" action="?m=timesheet&a=configure">
<table border="0" cellpadding="2" cellspacing="1">
<tr>
<td>
<fieldset><legend><b>Encriptação de Senha</b></legend>
<table>
	<input type="hidden" name="forcesubmit" value="true" />
	<tr>
		<td align="right"><?php echo $AppUI->_('Secret Key');?>:</td>
		<td>
			<input type="text" class="text" name="my_crypt_key" size="17" maxlength="16" value="<?php echo $my_crypt_key;?>"/>
		</td>
	</tr>
	<tr>

		<td align="right">Iv:</td>
		<td>
			<input type="text" class="text" name="my_crypt_iv" size="17" maxlength="16" value="<?php echo $my_crypt_iv;?>"/>
		</td>
	</tr>
</table>
</fieldset>
</td>
</tr>
<tr>
<td>
<fieldset><legend><b>Intervalo de Sincronização (Em horas)</b></legend>
<table>
	<tr>
		<td align="right"><?php echo $AppUI->_('Task');?>:</td>
		<td>
			<input type="text" class="text" name="task_sync_interval" size="17" maxlength="6" value="<?php echo $task_sync_interval;?>"/>
		</td>
	</tr>
	<tr>

		<td align="right"><?php echo $AppUI->_('Task Log');?>:</td>
		<td>
			<input type="text" class="text" name="task_log_sync_interval" size="17" maxlength="6" value="<?php echo $task_log_sync_interval;?>"/>
		</td>
	</tr>
</table>
</fieldset>
</td>
</tr>
	<tr>
	<td>
		<input type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitFrm('frmTimeSheet')" />
	</td>
	</tr>
</table>
</form>

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
GLOBAL $AppUI, $user_id;
$user_id    = $AppUI->user_id;

//Encriptação de senha
$cipher     = "rijndael-128";
$mode       = "cbc";
//FIM

$sql = "SELECT * FROM dot_calendar_config";
$registro = db_loadList( $sql );
foreach ($registro as $row) {
	$my_crypt_key = $row['my_crypt_key'];// Originalmente: "01234567890abcde"; 
	$my_crypt_iv  = $row['my_crypt_iv'];// Originalmente: "fedcba9876543210"; 
}
//Encriptação de senha
$td = mcrypt_module_open($cipher, "", $mode, $my_crypt_iv);
mcrypt_generic_init($td, $my_crypt_key, $my_crypt_iv);
//FIM

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
$sql = "SELECT * FROM google_account where user_id=".$user_id;
$registro = db_loadList( $sql );
foreach ($registro as $row) {
	$google_account_id = $row["google_account_id"];
	$google_account_password = $row['google_account_password'];
	$google_account_email = $row['google_account_email'];
}

$AppUI->savePlace();
$q  = new DBQuery;

$cyper_text = mcrypt_generic($td, $_POST['google_account_password']);

if ($google_account_id>0 && isset( $_POST['forcesubmit'] )) {
	$q->addTable('google_account');
	$q->addUpdate('google_account_email', $_POST['google_account_email']);
	$q->addUpdate('google_account_password', bin2hex($cyper_text)); //Encriptação de senha
	$q->addWhere('user_id', $user_id);
	if (!$q->exec()) {
		$AppUI->setMsg( db_error(), UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "Configurado", UI_MSG_OK );
	}
	$q->clear();
	$AppUI->redirect( 'm=timesheet&a=editgoogle' );
} else if (isset( $_POST['forcesubmit'] ) && !$google_account_id>0){
	$q->addTable('google_account');
	$q->addInsert('google_account_email', $_POST['google_account_email']);
	$q->addInsert('google_account_password', bin2hex($cyper_text)); //Encriptação de senha
	$q->addInsert('user_id', $user_id);
	if (!$q->exec()) {
		$AppUI->setMsg( db_error(), UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "Configurado", UI_MSG_OK );
	}
	$q->clear();
	$AppUI->redirect( 'm=timesheet&a=editgoogle');
}
//Encriptação de senha
mcrypt_generic_deinit($td);
mcrypt_module_close($td);
//FIM

// setup the title block
$titleBlock = new CTitleBlock( $AppUI->_('Configure Google Account'), 'timesheet.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=timesheet", $AppUI->_('Task Log') );
$titleBlock->show();

?>

<script language="javascript">
function submitFrm( frmName ) {

	eval('document.'+frmName+'.submit();');

}
</script>
<form name="frmTimeSheet" method="post" action="?m=timesheet&a=editgoogle">
<table border="0" cellpadding="2" cellspacing="1">
	<input type="hidden" name="forcesubmit" value="true" />
	<tr>
		<td align="right">Email:</td>
		<td>
			<input type="text" class="text" name="google_account_email" size="41" maxlength="40" value="<?php echo $google_account_email;?>"/>
		</td>
	</tr>
	<tr>

		<td align="right"><?php echo $AppUI->_('Password');?>:</td>
		<td>
			<input type="password" class="text" name="google_account_password" size="41" maxlength="40" value="<?php echo $google_account_password;?>"/>
		</td>
	</tr>
	<tr>
	<td></td>
	<td>
		<input type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitFrm('frmTimeSheet')" />
	</td>
	</tr>
</table>

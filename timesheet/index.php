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

// this is the index site for our timesheet module

// it is automatically appended on the applications main ./index.php

// by the dPframework

GLOBAL $user_id;
$user_id    = $AppUI->user_id;

$mes = date("m");
$ano = date("o");
$inicio = $ano.'-'.$mes.'-01';
$diaLimite = '';
if ($mes==2){
	if (($ano % 4 == 0) && ( (!($ano % 100 == 0)) || ($ano % 400 == 0))){
		$diaLimite = '29';
	} else { 
		$diaLimite = '28';
	}
}
else {
	if ($mes==4 || $mes==6 || $mes==9 || $mes==11) {
		$diaLimite = '30';
	} else {
		$diaLimite = '31';	
	}
}
$fim = $ano.'-'.$mes.'-'.$diaLimite;


// we check for permissions on this module

$canRead = !getDenyRead( $m );		// retrieve module-based readPermission bool flag

$canEdit = !getDenyEdit( $m );		// retrieve module-based writePermission bool flag



if (!$canRead) {			// lock out users that do not have at least readPermission on this module

	$AppUI->redirect( "m=public&a=access_denied" );

}



$AppUI->savePlace();	//save the workplace state (have a footprint on this site)



// retrieve any state parameters (temporary session variables that are not stored in db)



if (isset( $_GET['tab'] )) {

	$AppUI->setState( 'TimesheetIdxTab', $_GET['tab'] );		// saves the current tab box state

}

$tab = $AppUI->getState( 'TimesheetIdxTab' ) !== NULL ? $AppUI->getState( 'TimesheetIdxTab' ) : 0;	// use first tab if no info is available

$active = intval( !$AppUI->getState( 'TimesheetIdxTab' ) );						// retrieve active tab info for the tab box that

													// will be created down below

// we prepare the User Interface Design with the dPFramework



// setup the title block with Name, Icon and Help

$titleBlock = new CTitleBlock( 'Timesheet', 'timesheet.png', $m, "$m.$a" );	// load the icon automatically from ./modules/timesheet/images/

$titleBlock->addCell();
// adding the 'add'-Button if user has writePermissions

$titleBlock->addCell(

		'<input type="submit" class="button" value="Relatório">', '',

		'<form action="?m=projects&a=reports&project_id=0&report_type=tasklogs&log_start_date='
		.$inicio.'&log_end_date='.$fim.'&log_userfilter='.$user_id.'&do_report=salvar" method="post">', '</form>');

$titleBlock->addCell(

		'<input type="submit" class="button" value="Relatório Mensal Consolidado">', '',

		'<form action="?m=projects&a=reports&project_id=0&report_type=tasklogs_consolidadas&log_start_date='
		.$inicio.'&log_end_date='.$fim.'&log_userfilter=0&do_report=salvar" method="post">', '</form>');

$titleBlock->addCell(

		'<input type="submit" class="button" value="'.$AppUI->_('Configure Google Account').'">', '',

		'<form action="?m=timesheet&a=editgoogle" method="post">', '</form>'	//call addedit.php in case of mouseclick

	);
$titleBlock->show();	//finally show the titleBlock

// now prepare and show the tabbed information boxes with the dPFramework



// build new tab box object

$tabBox = new CTabBox( "?m=timesheet", "{$dPconfig['root_dir']}/modules/timesheet/", $tab );

$tabBox->add( 'formulario_horas', $AppUI->_('Task Log') );// add a subsite formulario_horas.php to the tab box object with title 'Cadastro de horas'

$tabBox->show(); // finally show the tab box



// this is the whole main site!

// all further development now has to be done in the files addedit.php, formulario_horas.php

// and in the subroutine do_quote_aed.php

?>
<script language="JavaScript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
      idate = eval( 'document.editFrm.task_' + field + '.value' );
  //      idate = eval( 'document.editFrm.' + field + '2.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=270, scrollbars=no' );
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

function calcula(){
	horas = document.rodape.horas.value;
	diaria = document.rodape.diaria.value;
	mes = document.rodape.mes.value;
	debito = horas-(diaria*mes);
	document.rodape.debito.value = debito;
}
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
?>
function delIt2(id) {
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Task Log', UI_OUTPUT_JS).'?';?>" )) {
		document.frmDelete2.task_log_id.value = id;
		document.frmDelete2.submit();
	}
}
<?php } ?>
</script>
<div class="info" style="margin:4px;">
  <form  id="rodape" action="" method="post" name="rodape">
	<?php 
	$sql = "SELECT task_log_hours
		FROM task_log
		WHERE task_log_date >= '".
		$inicio.
		"' AND task_log_date <= '".
		$fim."' AND task_log_creator=$user_id";
	$lista = db_loadList( $sql );
	$hrs = 0;
	foreach ($lista as $row) {
		$hrs += (float)$row["task_log_hours"];	
	}
	/*$minutes = (int) (( $hrs - ((int)  $hrs ))*60);
	$minutes = ((strlen($minutes) == 1) ? ('0'.$minutes) : $minutes);
	$s = (int)  $hrs .':'. $minutes;
	echo '<br><b>Total de horas no mês atual: </b>'.$hrs.'&nbsp;&nbsp; ('.$s .')&nbsp;&nbsp; |';	*/
	?>
	<br>
           <b>Horas trabalhadas no mês:&nbsp;&nbsp;</b>
<input type="text" id="horas" name="horas" value="<?php echo $hrs; ?>" readonly size="5" />
&nbsp;&nbsp; |&nbsp;&nbsp;
           <b>Carga horária diária:</b> <input name="diaria" type="text" id="diaria" size="5" value="" />
&nbsp;&nbsp; |&nbsp;&nbsp;
           <b>Dias úteis no mês:</b> <input name="mes" type="text" id="mes" size="5" value="" />  
&nbsp;&nbsp; |&nbsp;&nbsp;
           <b>Débito da horas:</b>
<input type="text" name="debito" id="debito" size="5" value="" readonly />  
<input type="button" name="calcular" id="calcular" value="<?php echo $AppUI->_('Calculate');?>" onclick="calcula()" />
</form>
</div>

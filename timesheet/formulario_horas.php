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
// this is an easy example showing how to use some of the UserInterface methods provided by the dPframework
// we will not have any database connection here

// as we are now within the tab box, we have to state (call) the needed information saved in the variables of the parent function
GLOBAL $AppUI, $canRead, $canEdit, $canDelete, $m, $user_id;
$user_id    = $AppUI->user_id;
if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=public&a=access_denied" );
}
?>
<script language="JavaScript">
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
<?php } 

// pull users
$q  = new DBQuery;
$q->addTable('tasks','t');
$q->addTable('projects','p');
$q->addTable('user_tasks','u');
$q->addQuery('t.task_id');
$q->addQuery('CONCAT_WS(" - ",p.project_short_name, t.task_name)');
$q->addOrder('p.project_short_name, t.task_name');
$q->addWhere('t.task_project = p.project_id and t.task_dynamic = 0 and t.task_percent_complete!=100 and u.task_id=t.task_id and u.user_id='.$user_id);
$q->addWhere('p.project_status!=7 and p.project_status!=4');//[7] Projetos Arquivados e [4] Projetos Em Espera
$tasks = $q->loadHashList();
$start_date = intval("") ? new CDate() : new CDate();
$start_date->subtractSpan( new Date_Span( "6,0,0,0" ) );

?>

window.onload = function(){
	processa('mostra')
}

var xmlHttp

function remove(task_log_id){
	if(confirm("Tem certeza que deseja remover este registro de tarefa?")){
		processa('remove', task_log_id)
	}       
}

function adiciona(){
	var form=window.document.getElementsByName("editFrm")[0]
	var task_log_description = form.task_log_description.value
	var task_log_hours = form.task_log_hours.value.replace(/^\s*/, "").replace(/\s*$/, "")
	task_log_description = task_log_description.replace(/^\s*/, "").replace(/\s*$/, "")	
	var progresso = form.progresso.checked
	if(task_log_description == ''){
		alert('Por favor, insira um comentário.')
	} else if(task_log_hours == ''){
		alert('Por favor, preencha as horas trabalhadas.')
	} 
		else if(progresso){
		if(confirm("Tem certeza que deseja finalizar esta tarefa?")){
			form.submit()
		}
	}
	else {
		processa('insert')
		form.reset()
	}
}
			
function processa(str, task_log_id){
	
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	var task_log_creator = '<?php echo $user_id;?>'
	var start_date = '<?php echo $start_date->format( FMT_DATETIME_MYSQL );?>'
	var form=window.document.getElementsByName("editFrm")[0]
	var task_log_description = form.task_log_description.value
	var task_log_hours = form.task_log_hours.value.replace(/^\s*/, "").replace(/\s*$/, "")
	if (task_log_hours == ""){
		task_log_hours=0;	
	}
	var progresso = form.progresso.checked
	var task_log_task = form.task_log_task.value
	var task_log_date = form.task_log_date.value	
	var url="./modules/timesheet/"+str+"log.php"
	url=url+"?task_log_description="+task_log_description+"&task_log_task="+task_log_task
	+"&task_log_date="+task_log_date+"&task_log_hours="+task_log_hours+"&task_log_id="+task_log_id
	+"&task_log_creator="+task_log_creator+"&start_date="+start_date+"&progresso="+progresso
	xmlHttp.onreadystatechange=stateChanged
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)	
}	

function GetXmlHttpObject(){
	var xmlHttp=null;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e){
		//Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

function stateChanged(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		document.getElementById("txtHint").innerHTML=xmlHttp.responseText
	}
}

</script>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<form name="frmDelete2" action="./index.php?m=tasks" method="post">
	<input type="hidden" name="dosql" value="do_updatetask">
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="task_log_id" value="0" />
</form>
</tr>
<?php
$log = new CTimesheet();
$df = $AppUI->getPref( 'SHDATEFORMAT' );
$log_date = new CDate( $log->task_log_date );

?>
<div style="margin:5px;font-size:14px;">
Registro de tarefas não finalizadas dos últimos 7 dias
</div>
<div id="txtHint"></div>
<form name="editFrm" action="./index.php?m=timesheet" method="post">
	<input type="hidden" name="dosql" value="do_log_aed" />
	<input type="hidden" name="task_log_id" value="<?php echo $task_log_id;?>" />
	<input type="hidden" name="task_log_name" value="<?php echo $task_log_name;?>" />
	<input type="hidden" name="task_log_creator" value="<?php echo $user_id;?>" />
  <tr valign="top">
	<td width="3%" class="fundoLinha"></td>
    <td  width="12%" class="fundoLinha">
      
	<input type="hidden" name="task_log_date" value="<?php echo $log_date->format( FMT_DATETIME_MYSQL );?>">
      <input type="text" name="log_date" size="10" value="<?php echo $log_date->format( $df );?>" class="text" disabled="disabled">
      <a href="#" onClick="popCalendar('log_date')">
	<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
      </a>
    </td>
    <td  width="25%" class="fundoLinha">
	 <?php echo arraySelect( $tasks, 'task_log_task', 'size="1" style="width:200px;" class="text"', $row->task_log_task? $row->task_log_task : $AppUI->task_log_task ) ?>
    </td>	
    <td  width="37%" class="fundoLinha">
	<textarea name="task_log_description" class="textarea" cols="50" rows="6"><?php echo $log->task_log_description;?></textarea>
    </td>
     <td  width="10%" class="fundoLinha"><b><?php echo $AppUI->_('End Task?');?> </b><input type=checkbox name="progresso" ></td>
    <td  width="10%" class="fundoLinha">
	<input name="task_log_hours" type="text" id="task_log_hours" value="<?php echo dPformSafe( $log->task_log_hours );?>" size="10" width="60" />
    </td>
	<td  width="3%" class="fundoLinha">
		<img style="cursor:pointer;" title="Adicionar Registro" src="./modules/timesheet/images/inserir.png" onclick="adiciona()" />
	</td>
<tr>
</form>
</table>

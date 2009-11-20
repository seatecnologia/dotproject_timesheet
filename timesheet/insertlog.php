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
//Campos enviados pela função javascript adiciona();
$task_log_task=$_GET["task_log_task"];
$task_log_description=$_GET["task_log_description"];
$task_log_hours=$_GET["task_log_hours"];
$task_log_date=$_GET["task_log_date"];
$task_log_creator=$_GET["task_log_creator"];
$start_date=$_GET["start_date"];
$progresso=$_GET["progresso"];

//Conexão com o banco de dados
$con = mysql_connect('localhost', 'root', '');
if (!$con){
	die('Could not connect: ' . mysql_error());
}
mysql_select_db("dotproject", $con);


//Verifica se é para finalizar a tarefa
//e altera a tarefa no banco de dados 
if($progresso=='true'){
	$sql = "UPDATE tasks set task_percent_complete = 100 WHERE task_id=".$task_log_task;
	mysql_query($sql);
}

//Buscando nome da tarefa para inclusão do sumário
$sql1 = "SELECT task_name FROM tasks WHERE task_id=".$task_log_task;
$result = mysql_query($sql1);
while($row = mysql_fetch_array($result)){
	$task_log_name = $row['task_name'];
}

//Inserindo o log no banco de dados
$sql="insert into task_log (task_log_name, task_log_creator, task_log_task, task_log_description, task_log_hours, task_log_date)".
" values ('".$task_log_name."', ".$task_log_creator.",".$task_log_task.", '".$task_log_description."', ".$task_log_hours.", '".$task_log_date."')";
$res = mysql_query($sql);

//Buscando os logs do usuário para apresentação na tela
$sql="SELECT 
              task_log.*, task_name, task_percent_complete  
       FROM task_log
               LEFT JOIN tasks ON task_log.task_log_task = task_id
               LEFT JOIN users ON user_id = task_log_creator
       WHERE 
               user_id=".$task_log_creator." AND  task_log_date >= '".$start_date.
               "' AND task_percent_complete!=100 ORDER BY task_log_date";
$result = mysql_query($sql);

//preparando fragmento HTML
echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"tbl\">
	<tr>
		<th width=\"3%\"></th>
		<th width=\"12%\">Data</th>
		<th width=\"25%\">Tarefa</th>
		<th width=\"37%\">Descri&ccedil;&atilde;o</th>
		<th width=\"10%\">Progresso</th>
		<th width=\"10%\">Horas Trabalhadas</th>
		<th width=\"3%\"></th>
	</tr>";

while($row = mysql_fetch_array($result)){
$data = $row['task_log_date'];
$data = (substr($data,8,2).'/'.substr($data,5,2).'/'.substr($data,0,4));
 echo "<tr>";
 echo "<td>".'<a href="./index.php?m=timesheet&a=addedit&task_log_id=' . $row["task_log_id"] . '&task_name=' . $row["task_name"] . '">';
 echo "<img style=\"cursor:pointer;\" title=\"Editar Registro\" src=\"./images/icons/stock_edit-16.png\" border=\"0\"/>";
 echo "</a></td>";
 echo "<td>" . $data . "</td>";
 echo "<td>". $row['task_name'] ."</td>";
 echo "<td>" . $row['task_log_description'] . "</td>";
 echo "<td>". $row['task_percent_complete'] ."%</td>";
 echo "<td>" . $row['task_log_hours'] . "</td>";
 echo "<td><img style=\"cursor:pointer;\" title=\"Remover Registro\" src=\"./modules/timesheet/images/Imagem3.png\" onclick=\"remove(".
	$row['task_log_id'].")\" /></td>";
 echo "</tr>";
}
echo "</table>";

//Fim da conexão com o banco de dados
mysql_close($con);

?>

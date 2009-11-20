<?php
$task_log_id=$_GET["task_log_id"];
$task_log_creator=$_GET["task_log_creator"];
$start_date=$_GET["start_date"];

$con = mysql_connect('localhost', 'root', '');

if (!$con){
	die('Could not connect: ' . mysql_error());
}

mysql_select_db("dotproject", $con);

$sql="delete from task_log where task_log_id=".$task_log_id;

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

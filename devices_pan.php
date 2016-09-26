<html> <body>
<?php
/*Ligação à base de dados*/
$host = "db.ist.utl.pt";
$user = "ist175661";
$pass = "gejc9717";
$dsn = "mysql:host=$host;dbname=$user"; try{
$connection = new PDO($dsn, $user, $pass); }catch(PDOException $exception){
echo("<p>Error "); echo($exception->getMessage()); echo("</p>");
exit();
} $patientnumber=$_REQUEST['patient_name']; /*Query*/
$sql="SELECT c.pan, c.manuf, c.snum, c.start, c.end
FROM Patient as p, Wears as w, Connects as c
p.number = w.patient
WHERE w.patient = '$patientnumber' AND w.pan = c.pan AND
AND c.start >= w.start AND c.end <= w.end ORDER BY c.pan,c.manuf,c.snum";
$result = $connection->query($sql);
if ($result == FALSE){
$info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>"); exit();
}
/*Display dos Devices*/
echo("<h3>PANs and Devices:</h3>");
echo("<table border=\"1\">");
echo("<tr><td>PAN domain<td>Manufacturer</td><td>Serial
Number</td><td>Datetime Start</td><td>DateTime End</td></tr>"); foreach($result as $row){
as SerialNumber and w.pan = c.pan
echo("<tr><td>"); echo($row['pan']); echo("</td><td>"); echo($row['manuf']);
echo("</td><td>"); echo($row['snum']);
echo("</td><td>"); echo($row['start']); echo("</td><td>");
echo($row['end']); echo("</td></tr>");
} echo("</table>");
/*Query da penultima pan e da ultima pan*/
/*Testada*/
$pan_actual_sql = "select c.pan as PAN, c.manuf as Manufacturer, c.snum
from Wears as w, Connects as c,Patient as p
where p.number = $patientnumber and p.number= w.patient
and c.start >= w.start and c.end <= w.end and c.end > current_timestamp";
$pan_actual = $connection->query($pan_actual_sql);
if ($pan_actual == FALSE){
$info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>"); exit();
}
/*Testada*/
$pan_anterior_sql = "select c.pan as PAN, c.manuf as Manufacturer, c.snum
as SerialNumber
from Patient as p, Wears as w, Connects as c
where p.number =$patientnumber and p.number = w.patient and w.pan = c.pan
and c.start >= w.start and c.end <= w.end and c.end <= current_timestamp
and c.end >= all(select c.end from Patient as p, Wears as w,Connects as c
where p.number = $patientnumber and p.number = w.patient and w.pan = c.pan
and c.start >= w.start and c.end <= w.end and c.end <= current_timestamp)
and (c.manuf,c.snum) not in(select c.manuf,c.snum from Patient as p,Wears as w, Connects as c
where p.number = $patientnumber and p.number = w.patient and w.pan = c.pan
and c.start >= w.start and c.end <=w.end and c.end > current_timestamp)";
$pan_anterior = $connection->query($pan_anterior_sql);
if ($pan_anterior == FALSE){
$info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>"); exit();
}
/*Display do penultimo e ultimo*/
echo("<h3>Actual PAN and Devices:</h3>");
echo("<table border=\"1\">");
echo("<tr><td>PAN domain</td><td>Manufacturer</td><td>Serial
Number</td></tr>");
foreach($pan_actual as $row){
echo("<tr><td>"); echo($row['PAN']); echo("</td><td>"); echo($row['Manufacturer']); echo("</td><td>"); echo($row['SerialNumber']); echo("</td></tr>"); $pan_actualdomain=$row['PAN'];
} echo("</table>");
echo("<h3>Which Devices do you want to transfer?</h3>"); echo("<table border=\"1\">");
/* echo("<tr><td>PAN domain</td><td>Manufacturer</td><td>Serial Number</td></tr>");
foreach($pan_anterior as $row2){ echo("<tr><td>");
echo($row2['PAN']); echo("</td><td>"); echo($row2['Manufacturer']); echo("</td><td>"); echo($row2['SerialNumber']); echo("</td></tr>");
}
echo("</table>"); /*Transferência de Devices*/
/*Transferência de Devices*/
$nrowsant=$pan_anterior->rowCount(); if($nrowsant <> 0){
echo("<form action=\"transfer.php\">"); foreach($pan_anterior as $row3){
$arr=array($row3['Manufacturer'], $row3['SerialNumber']); $device = implode("§", $arr);
echo("<input type=\"checkbox\" name=\"device[]\"
value=\"$device\"> Manufacturer: $row3[Manufacturer], SerialNumber: $row3[SerialNumber]<br/>");
}
echo("<input type=\"hidden\" name=\"pan\" value=\"$pan_actualdomain\"/>");

echo("<p></p>");
echo("<input type=\"submit\" value = \"Submit\">");
echo("</form>"); }else{
echo("<p>No devices to transfer.</p>"); }
?> </body>
</html>

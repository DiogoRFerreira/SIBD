<html>
  <body>
<?php
/*Ligação à base de dados*/
$host="db.ist.utl.pt";
$user="ist175661";
$pass="gejc9717";
$dsn="mysql:host=$host; dbname=$user";

try{
  $connection= new PDO($dsn,$user,$pass);
}catch(PDOException $exception){
  echo("<p>Error: ");
  echo($exception->getMessage());
  echo("</p>");
  exit();
}

$patientname = $_REQUEST['patient_name']; /*Pacient exists?*/
$sql5="SELECT number, name FROM Patient
        WHERE name like '%$patientname%' ORDER BY number";
$result5 = $connection->query($sql5);

if ($result5 == FALSE){
  $info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>");
  exit();
}
$nrows5=$result5->rowCount();
if($nrows5 <> 0){

/*Query Readings*/
$sql="SELECT p.name,p.number,r.datetime,r.manuf,r.snum,r.value,s.units FROM Reading as r,Sensor as s,Connects as c,Wears as w,Patient as p Where p.name like '%$patientname%' and p.number=w.patient and w.pan=c.pan and w.start <= c.start and w.end >= c.end and c.snum=s.snum and c.manuf = s.manuf and s.snum= r.snum and s.manuf = r.manuf and r.datetime >= c.start and r.datetime <=c.end order by p.number,r.datetime,r.manuf";
$result = $connection->query($sql);
if ($result == FALSE){
  $info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>");
  exit();
}
$nrows=$result->rowCount();
if($nrows <> 0){
  /*Display da tabela_Readings*/
  echo("<h3>Readings</h3>");
  echo("<table border=\"1\">");
  echo("<tr><td>Number<td>Name</td><td>DateTime</td><td>Manufacturer</td><td> SerialNumber</td><td>Value</td><td>Units</td></tr>");
  foreach($result as $row){
    echo("<tr><td>");echo($row['number']); echo("</td><td>"); echo($row['name']); echo("</td><td>"); echo($row['datetime']); echo("</td><td>"); echo($row['manuf']); echo("</td><td>"); echo($row['snum']); echo("</td><td>"); echo($row['value']); echo("</td><td>"); echo($row['units']); echo("</td></tr>");
}
echo("</table>");
}else{
  echo("<p>Patient has 0 Readings</p>");
}

/*Query Settings*/
$sql2="Select p.name,p.number,s.datetime,s.manuf,s.snum,s.value,a.units from Setting as s,Actuator as a,Connects as c,Wears as w,Patient as p where p.name like '%$patientname%' and p.number = w.patient and w.pan=c.pan and w.start<= c.start and w.end >=c.end and c.snum = a.snum and c.manuf=a.manuf and a.snum=s.snum and a.manuf= s.manuf and s.datetime >= c.start and s.datetime <= c.end order by p.number,s.datetime,s.manuf";
$result2 = $connection->query($sql2);
if ($result2 == FALSE){
  $info = $connection->errorInfo();
  echo("<p>Error: {$info[2]}</p>");
  exit();
}
if($nrows2 <> 0){ /*Display da Tabela Settings*/
echo("<h3>Settings</h3>"); echo("<table border=\"1\">");
echo("<tr><td>Number</td><td>Name</td><td>DateTime</td><td>Manufacturer</td><td>S erialNumber</td><td>Value</td><td>Units</td></tr>");
foreach($result2 as $row){ echo("<tr><td>");
echo($row['number']); echo("</td><td>"); echo($row['name']); echo("</td><td>");
echo($row['datetime']); echo("</td><td>"); echo($row['manuf']); echo("</td><td>"); echo($row['snum']); echo("</td><td>");
echo($row['value']); echo("</td><td>"); echo($row['units']);
$nrows2=$result2->rowCount();
echo("</td></tr>"); echo("</table>");
}else{echo("<p>Patient has 0 Settings</p>");} }else{echo("<p>Patient not found!</p>");} $connection = NULL;
?>
</body>
</html>

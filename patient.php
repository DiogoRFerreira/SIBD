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
}
$patientname = $_REQUEST['patient_name']; /*Query*/
$sql="SELECT number, name FROM Patient
WHERE name like '%$patientname%' ORDER BY number";
$result = $connection->query($sql);
if ($result == FALSE){
$info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>"); exit();
} $nrows=$result->rowCount(); if($nrows <> 0){
/*Escolha do paciente*/ echo("<h3>Patient:</h3>");
echo("<form action=\"devices_pan.php\">"); foreach($result as $row){
echo("<input type=\"radio\" name=\"patient_name\" value=\"$row[number]\" />Number: $row[number] Name: $row[name]<br/>");
}
echo("<p><input type=\"submit\"
16
value=\"Submit\"/></p></form>"); }else{
echo("<p>Patient not found!</p>"); }
?>
</body> </html>

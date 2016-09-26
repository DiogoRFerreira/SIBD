<html> <body>
<?php
$host = "db.ist.utl.pt";
$user = "ist175661";
$pass = "gejc9717";
$dsn = "mysql:host=$host;dbname=$user";
try {
$connection = new PDO($dsn, $user, $pass); }
catch(PDOException $exception) {
echo("<p>Error: "); echo($exception->getMessage()); echo("</p>");
exit();
}
$result = $_REQUEST['device']; $pan=$_REQUEST['pan'];
echo("<h3>Devices inseridos:</h3>");
21
foreach($result as $devices){
$device = explode("§", $devices);
echo("<p>Manufacturer: $device[0] e Serial Number: $device[1]</p>"); /*$c_time_sql = "select current_timestamp";
$c_time = $connection->query($c_time_sql);*/
$periods_sql = "select * from Period where start = current_timestamp and
end = '2030-12-31 00:00:00'";
$periods = $connection->query($periods_sql);
$nrows = $periods->rowCount(); if($nrows == 0){
$insert_period_sql = "insert into Period values(current_timestamp,
'2030-12-31 00:00:00')";
$insert_period = $connection->exec($insert_period_sql); }
$insert_sql = "insert into Connects values(current_timestamp, '2030-12-31 00:00:00','$device[1]', '$device[0]', '$pan')";
$insert = $connection->exec($insert_sql);
if ($insert == FALSE){
$info = $connection->errorInfo(); echo("<p>Error: {$info[2]}</p>"); exit();
} }
$connection = NULL;
?>
</body>
</html>

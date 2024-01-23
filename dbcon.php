<?php




$host = '.'; // Replace with your server name or IP address
$dbname = 'UIU_Data_Repo'; // Replace with your database name
$username = 'sa'; // Replace with your database username
$password = 'SQLs3rv3r'; // Replace with your database password

$dsn = "sqlsrv:Server=$host;Database=$dbname";
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

try {
    $conn = new PDO($dsn, $username, $password, $options);
    //echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>



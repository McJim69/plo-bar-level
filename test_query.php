<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'McJim');
define('DB_PASSWORD', 'Restricted654123');
define('DB_NAME', 'paga_sanpedro');

$link = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);

$stmt = $link->prepare("select * from voters where vin = 18");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>

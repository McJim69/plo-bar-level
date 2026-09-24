<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("config.php");
$link = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
	PDO::ATTR_EMULATE_PREPARES => true,
]);

$value = '';
$sql_cond = " where (vname like ? or remarks like ? or ato like ? or vin like ?)";
$params = ['%' . $value . '%', '%' . $value . '%', '%' . $value . '%', '%' . $value . '%'];
$stmt = $link->prepare("select count(*) from voters" . $sql_cond);
if ($stmt->execute($params)) {
    echo "Count 1: " . $stmt->fetchColumn() . "\n";
} else {
    echo "Error 1\n";
}

$mun_val = "PAGADIAN";
$sql_cond .= " and city_mun = ?";
$params[] = $mun_val;

$bar_val = "SAN PEDRO";
$sql_cond .= " and barangay = ?";
$params[] = $bar_val;

$stmt = $link->prepare("select count(*) from voters" . $sql_cond);
if ($stmt->execute($params)) {
    echo "Count 2: " . $stmt->fetchColumn() . "\n";
} else {
    echo "Error 2\n";
}

$stmt = $link->prepare("select * from voters" . $sql_cond . " order by vname LIMIT 0, 10");
if ($stmt->execute($params)) {
    echo "Fetch count: " . count($stmt->fetchAll()) . "\n";
} else {
    echo "Error 3\n";
}
?>

<?php
require_once "pdo.php";

if (!isset($_SESSION['user_id'])){
  die("ACCESS DENIED");
}
header('Content-Type: application/json; charset=utf-8');
$stmt = $pdo->prepare('SELECT name FROM institution WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
?>
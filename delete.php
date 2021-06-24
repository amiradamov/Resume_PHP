<?php
	require_once "pdo.php";

	$sql = "DELETE FROM profile WHERE profile_id = :pi";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(':pi' => $_REQUEST['profile_id']));
	header("Location: index.php");
?>
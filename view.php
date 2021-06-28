<?php
	require_once "pdo.php";
	require_once "utill.php";

	if(isset($_POST['done'])) {
		header("Location: index.php");
		return;
	}	

	if (! isset($_REQUEST['profile_id'])) {
		$_SESSION['error'] = "Missing profile_id";
		header('Location: index.php');
		return;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>View</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
	<div class="form">
		<h1>Profile information</h1>
		<div >
			<?php

			$sql = "SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :pid";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':pid' => $_REQUEST['profile_id']));

			while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<p>First Name: ";
				echo ($info['first_name']);
				echo "</p>";
				echo "<p>Last Name: ";
				echo ($info['last_name']);
				echo "</p>";
				echo "<p>Email: ";
				echo ($info['email']);
				echo "</p>";
				echo "<p>Summary: ";
				echo ($info['summary']);
				echo "</p>";
				echo "<p>Position</p>";
			}
			$sql = "SELECT year, description FROM position WHERE profile_id = :pid";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':pid' => $_REQUEST['profile_id']));
			echo "<ul>";
			while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<li>";
				echo ($info['year'].": ".$info['description']);
				echo "</li>";
			}
			echo "</ul>";
			echo "Education";
			$sql = "SELECT year, name, rank FROM Education 
						JOIN Institution ON Education.institution_id = Institution.institution_id
						WHERE profile_id = :pid ORDER by rank";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':pid' => $_REQUEST['profile_id']));
			echo "<ul>";
			while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<li>";
				echo ($info['year'].": ".$info['name']);
				echo "</li>";
			}
			echo "</ul>";
			?>
		</div>
		<form method="POST">
		<button type="submit" name="done" class="button button-block">Done</button>
		<input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']); ?>">
		</form>
	</div>
</body>
</html>
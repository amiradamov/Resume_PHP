<?php
	require_once "pdo.php";
	require_once "utill.php";

	// if ( isset($_POST['delete']) && isset($_POST['id']) ) {
	//     $sql = "DELETE FROM myid WHERE user_id = :zip";
	//     $stmt = $pdo->prepare($sql);
	//     $stmt->execute(array(':zip' => $_POST['id']));
	//     $_SESSION['success'] = 'Record deleted';
	//     header( 'Location: index.php' ) ;
	//     return;
	// }

	// $stmt = $pdo->prepare("SELECT title, user_id FROM myid where user_id = :xyz");
	// $stmt->execute(array(":xyz" => $_GET['id']));
	// $row = $stmt->fetch(PDO::FETCH_ASSOC);
	// if ( $row === false ) {
	//     $_SESSION['error'] = 'Bad value for id';
	//     header( 'Location: index.php' ) ;
	//     return;
	// }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>index</title>
	<script type="text/javascript" src="jquery.in.js"></script>
</head>
<body>
	<div id="view">
		<?php
			$nRows = $pdo->query('select count(*) from profile')->fetchColumn();
			$stmt = $pdo->query("SELECT * FROM profile");
			$stmt -> execute();

			if (isset($_SESSION['user_id'])) {
				echo('<p>Welcome, '.htmlentities($_SESSION['name']).'</p>');
				flashmessage();
				echo('<a href="logout.php">Logout</p></a>');

			if ($nRows > 0) {
			echo '<table border = "1"."\n"';
			echo '<tr><th>Name</th><th>Headline</th><th>Action</th></tr>';
			while ($profiles = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<tr><td>";
				echo ('<a href="view.php?profile_id='.$profiles['profile_id'].'">'.$profiles['first_name']." ".$profiles['last_name'].'</a>');
				echo ("</td><td>");
				echo ($profiles['headline']);
				echo ("</td><td>");
				echo ('<a href="edit.php?profile_id='.$profiles['profile_id'].'">Edit</a> / ');
				echo ('<a href="delete.php?profile_id='.$profiles['profile_id'].'">Delete</a>');
	
				echo "</td></tr>";
			}
			echo "</table>\n";
			}	

			echo ('<p><a href="add.php">Add New Entry</a></p>'."\n");
			}else {
				echo ('<p><a href="login.php">Login</a></p>'."\n");

				if ($nRows > 0) {
					echo '<table border = "1"."\n"';
					echo '<tr><th>Name</th><th>Headline</th></tr>';
					while ($profiles = $stmt->fetch(PDO::FETCH_ASSOC)) {
						echo "<tr><td>";
						echo ('<a href="view.php?profile_id='.$profiles['profile_id'].'">'.$profiles['first_name']." ".$profiles['last_name'].'</a>');
						echo ("</td><td>");
						echo ($profiles['headline']);
						echo "</td></tr>";
					}
					echo "</table>\n";
				}
			}
		?>
	</div>
</body>
</html>
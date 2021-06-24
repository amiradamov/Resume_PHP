<?php
require_once "pdo.php";
require_once "utill.php";

unset($_SESSION['name']);
unset($_SESSION['user_id']);

if (isset($_POST['login'])) {
	echo "POST";
	$user_email = trim($_POST['email']);
	$user_password = trim($_POST['password']);

	if (strlen($user_email) < 1 || strlen($user_password) < 1) {
		$_SESSION['error'] = 'Email and password are required';
		header('Location: login.php');
		return;
	}
	$stmt = $pdo->prepare ("SELECT user_id, name FROM user_id WHERE email = :em AND password = :ps");
	$stmt->execute(array(
			':em' => $_POST['email'],
			':ps' => $_POST['password']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($row !== false) {
		$_SESSION['name'] = $row['name'];
		$_SESSION['user_id'] = $row['user_id'];
		$_SESSION['success'] = 'logged in successfully.';
		header("Location: index.php");
		return;
	} else{
		$_SESSION['error'] = "Wrong email or password";
		header("Location: login.php");
		return;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>login</title>
</head>
<body>
	<div>
		<h1>LogIn</h1>
		<?php flashmessage();?>
		<form method="POST">
			<p>Email: <input type="text" name="email"></p>
			<p>Password: <input type="password" name="password"></p>
			<p>
				<input type="submit" name="login" value="Login">
				<a href="index.php">Cancel</a>
			</p>
		</form>
	</div>	
</body>
</html>
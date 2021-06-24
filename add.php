<?php
	require_once "pdo.php";
	require_once "utill.php";

	if (!isset($_SESSION['user_id'])) {
		die("ACCESS DENIED");
		return;
	}
	if (isset($_POST['cancel'])) {
		header("Location: index.php");
		return;
	}
	if (isset($_POST['add'])) {

		$msg = validateProfile();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: add.php");
			return;
		}
		$msg = validatePos();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: add.php");
			return;
		}
		$msg = validateEdu();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: add.php");
			return;
		}
		// Data is valid -- Time to insert
		$sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:ud, :fn, :ln, :em, :hl, :su)";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(array (
			':ud' => $_SESSION['user_id'],
			':fn' => $_POST['f_name'],
			':ln' => $_POST['l_name'],
			':em' => $_POST['email'],
			':hl' => $_POST['headline'],
			':su' => $_POST['summary']));
		$profile_id = $pdo->lastInsertId();

		// Insert the Position and Education entries
		insertPosition($pdo, $profile_id);
		insertEducation($pdo, $profile_id);
   		$_SESSION['success'] = "Profile added";
   		header("Location: index.php");
   		return;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>add</title>
	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

	  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

	  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
	<?php flashmessage();?>
	<form method="POST">
		<p>first name: <input type="text" name="f_name"></p>
		<p>last name: <input type="text" name="l_name"></p>
		<p>email: <input type="text" name="email"></p>
		<p>headline: <input type="text" name="headline" autocomplete="off"></p>
		<textarea name="summary" placeholder="Summary" rows="4" cols="40" style="resize: none;"></textarea>
		<p>Education: <button type="submit" name="addEdu" id="addEdu">+</button></p>
		<div id="education_fields"></div>
		<p>Position: <button type="submit" name="addPos" id="addPos">+</button>
		<div id="position_fields"></div>

		<p>
			<input type="submit" name="add" value="submit">
			<button type="submit" name="cancel">cancel</button>
		</p>
	</form>

	<script>
		countEdu = 0;
		countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
		$(document).ready(function(){
		    window.console && console.log('Document ready called');

			$('#addEdu').click(function(event) {
				event.preventDefault();
				if( countEdu >= 9) {
					alert("Maximum of nine education entries exceeded");
					return;
				}
				countEdu++;
				console.log("Adding education "+countEdu);
		        $('#education_fields').append(
		            '<div id="education'+countEdu+'"> \
		            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" />\
		            <input type="button" style value="-" \
		                onclick="$(\'#education'+countEdu+'\').remove(); countEdu--; return false;"></p> \
		            <p>Education: <input type="text" size="80" name="edu_desc'+countEdu+'" id="school" value"" autocomplete="off" /></p>\
		            </div>');

		        $('#school').autocomplete({
		        	source: "school.php"
		        });
		    });

		    $('#addPos').click(function(event){
		        // http://api.jquery.com/event.preventdefault/
		        event.preventDefault();
		        if ( countPos >= 9 ) {
		            alert("Maximum of nine position entries exceeded");
		            return;
		        }
		        countPos++;
		        window.console && console.log("Adding position "+countPos);
		        $('#position_fields').append(
		            '<div id="position'+countPos+'"> \
		            <p>Year: <input type="text" name="year'+countPos+'" value="" />\
		            <input type="button" style value="-" \
		                onclick="$(\'#position'+countPos+'\').remove(); countPos--; return false;"></p> \
		            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
		            </div>');
		    });
		});
	</script>

</body>
</html>
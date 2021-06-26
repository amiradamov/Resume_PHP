<?php
	require_once "pdo.php";
	require_once "utill.php";

	$url = "https://localhost/js/resume_final";
	// if the user is not logged in redirect back to index.php
	if (!isset($_SESSION['user_id'])) {
		die("ACCESS DENIED");
		return;
	}
	// if the user requested cancel go back to index.php
	if (isset($_POST['cancel'])) {
		header("Location: $url/index.php");
		return;
	}

	// Make sure the REQUEST parameter is present
	if (! isset($_REQUEST['profile_id'])) {
		$_SESSION['error'] = "Missing profile_id";
		header('Location: index.php');
		return;
	}

	// Load up the profile in question
	$sql = "SELECT * FROM profile WHERE profile_id = :pd AND user_id = :ud";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array (
		':pd' => $_REQUEST['profile_id'],
		':ud' => $_SESSION['user_id']));
	$profile = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($profile === false) {
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}

	// Handle the incoming data
	if (isset($_POST['add'])) {

		$msg = validateProfile();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
			return;
		}
		$msg = validatePos();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
			return;
		}
		$msg = validateEdu();
		if (is_string($msg)) {
			$_SESSION['error'] = $msg;
			header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
			return;
		}

		// Updating the date
		$sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
		WHERE profile_id = :pd AND user_id = :ud";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array (
		':pd' => $_REQUEST['profile_id'],
		':ud' => $_SESSION['user_id'],
		':fn' => $_POST['f_name'],
		':ln' => $_POST['l_name'],
		':em' => $_POST['email'],
		':he' => $_POST['headline'],
		':su' => $_POST['summary']));

		// Clear out the old position entries
		$stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
		$stmt->execute(array (':pid' => $_REQUEST['profile_id']));
		// Insert the Position entries
		insertPosition($pdo, $_REQUEST['profile_id']);

		// Clear out the old education entries
		$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
		$stmt->execute(array (':pid' => $_REQUEST['profile_id']));	

		// Insert the Education entries
		insertEducation($pdo, $_REQUEST['profile_id']);

   		$_SESSION['success'] = "Profile updated";
   		header("Location: index.php");
   		return;
	}
	// Load up the position and education rows
	$positions = loadPos($pdo, $_REQUEST['profile_id']);
	$schools = loadEdu($pdo, $_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>edit</title>
	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

	  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

	  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
	<?php flashmessage();?>
	<form method="POST">
		<input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']); ?>">
		<p>first name: <input type="text" name="f_name" value="<?= htmlentities($profile['first_name']); ?>"></p>
		<p>last name: <input type="text" name="l_name" value="<?= htmlentities($profile['last_name']); ?>"></p>
		<p>email: <input type="text" name="email" value="<?= htmlentities($profile['email']); ?>"></p>
		<p>headline: <input type="text" name="headline" value="<?= htmlentities($profile['headline']); ?>" autocomplete="off"></p>
		<textarea name="summary" placeholder="Summary" rows="4" cols="40" style="resize: none;"><?= htmlentities($profile['summary']); ?></textarea>
		<?php

			$edu = 0;
			echo('<p>Education: <button type="submit" name="addEdu" id="addEdu">+</button>'."\n");
			echo('<div id="education_fields">'."\n");
			if (count($schools) > 0){
				foreach($schools as $school ){
					$edu++;
					echo('<div id="education'.$edu.'">');
					echo('<p>Year: <input type="text" name="edu_year'.$edu.'" value="'.$school['year'].'"/>'."\n");
					echo('<input type="button" value="-" name="remove" onclick="$(\'#education'.$edu.'\').remove(); return false;"></p>'."\n");
					echo('<p>Education: <input type="text" size="80" name="edu_desc.'.$edu.'" class="school" value="'.htmlentities($school['name']).'" autocomplete = "off" />');
					echo('</div>');
					echo "<br>";
				}
			}
			echo('</div></p>'."\n");

			$pos = 0;
			echo('<p>Position: <button type="submit" name="addPos" id="addPos">+</button>'."\n");
			echo('<div id="position_fields">'."\n");
			if (count($positions) > 0) {
				foreach($positions as $position){
					$pos++;
					echo('<div id="position'.$pos.'">'."\n");
					echo('<p>Year: <input type="text" name="year'.$pos.'" value="'.$position['year'].'"/>'."\n");
					echo('<input type="button" value="-" onclick="$(\'#position'.$pos.'\').remove(); countPos--; return false;"></p>'."\n");
					echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
					echo(htmlentities($position['description']));
					echo("</textarea>\n");
					echo('</div>'."\n");
				}
			}
			echo('</div></p>'."\n");
		?>

		<p>
			<input type="submit" name="add" value="Update">
			<button type="submit" name="cancel">cancel</button>
		</p>
	</form>

	<script>
	countPos = <?= $pos ?>;
	countEdu = <?= $edu ?>;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
	$(document).ready(function(){
	    window.console && console.log('Document ready called');

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
	            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
	            <input type="button" value="-" \
	                onclick="$(\'#position'+countPos+'\').remove(); return false;"></p> \
	            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
	            </div>');
	    });

	    $('#addEdu').click(function(event){
	        event.preventDefault();
	        if ( countEdu >= 9 ) {
	            alert("Maximum of nine education entries exceeded");
	            return;
	        }
	        countEdu++;
	        window.console && console.log("Adding education "+countEdu);

	        // Grab some HTML with hot spots and insert into the DOM
	        var source = $("#edu-template").html();
	        $('#education_fields').append(source.replace(/@COUNT@/g,countEdu));
		
			// Add the even handler to the new ones
	        $('.school').autocomplete({
	            source: "school.php"
	        });

    	});	
  //   	// Add the even handler to the new ones
	 //    $('#school').autocomplete({
	 //   		source: "school.php"
		// });

	});
	</script>	

	<!-- HTML with Substitution hot spots -->
<script id="edu-template" type="text">
  <div id="education@COUNT@">
    <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
    <input type="button" value="-" name="remove" onclick="$('#education@COUNT@').remove(); countEdu--; return false;"><br>
    <p>Education: <input type="text" size="80" name="edu_desc@COUNT@" class="school" value="" autocomplete = "off"/>
    </p>
  </div>
</script>
</body>
</html>
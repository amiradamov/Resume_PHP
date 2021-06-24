<?php
	function flashmessage() {
		if(isset($_SESSION['error'])) {
			echo('<p style="color:red">'.htmlentities($_SESSION['error']).'</p>');
			unset($_SESSION['error']);
		}
		if(isset($_SESSION['success'])) {
			echo('<p style="color:green">'.htmlentities($_SESSION['success']).'</p>');
			unset($_SESSION['success']);
		}
	}
function validateProfile() {
	if (strlen($_POST['f_name']) == 0 || strlen($_POST['l_name']) == 0 ||
		strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 || 
		strlen($_POST['summary']) == 0) {
		return "All fields are required";
	}
	if (strpos($_POST['email'], '@') === false) {
		return "Email address must contain @";
	}
	return true;
}
function validatePos() {
	for ($i=1; $i < 9; $i++) { 
		if (! isset($_POST['year'.$i])) continue;
		if (! isset($_POST['desc'.$i])) continue;
		$year = $_POST['year'.$i];
		$desc = $_POST['desc'.$i];
		if (strlen($year) == 0 || strlen($desc) == 0) {
			return "All fields are required";
		}	
		if (!is_numeric($year)) {
			return "Position year must be numeric";
		}
	}
	return true;
}

function validateEdu() {
	for ($i=1; $i < 9; $i++) { 
		if (! isset($_POST['edu_year'.$i])) continue;
		if (! isset($_POST['edu_desc'.$i])) continue;
		$year = $_POST['edu_year'.$i];
		$desc = $_POST['edu_desc'.$i];
		if (strlen($year) == 0 || strlen($desc) == 0) {
			return "All fields are required";
		}
		if (!is_numeric($year)) {
			return "Education year must be numeric";
		}	
	}
}

// TODO: SHould validate education;

function loadPos($pdo, $profile_id) {
	$stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :pi ORDER by rank');
	$stmt->execute(array(':pi' => $profile_id));
	$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $positions;
}
function LoadEdu($pdo, $profile_id) {
	$stmt = $pdo->prepare('SELECT year, name FROM Education 
						JOIN Institution ON Education.institution_id = Institution.institution_id
						WHERE profile_id = :pi ORDER by rank');
	$stmt->execute(array(':pi' => $profile_id));
	$schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $schools;
}

function updateEducation($pdo, $profile_id) {
	$rank = 1;
	for($i=1; $i<=9; $i++) {
	    if ( ! isset($_POST['edu_year'.$i]) ) continue;
	    if ( ! isset($_POST['edu_desc'.$i]) ) continue;
	    $year = $_POST['edu_year'.$i];
	    $edu_desc = $_POST['edu_desc'.$i];

	    // Lookup the school if it is there.
	    $institution_id =false;
	    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
	    $stmt->execute(array(':name' => $edu_desc));
	    $row = $stmt->fetch(PDO::FETCH_ASSOC);
	    if ($row !== false) $institution_id = $row['institution_id'];

	    // if there was no institution, insert it
	    if ($row === false) {
	    	$stmt = $pdo->prepare('INSERT INTO institution (name) VALUE (:name)');
	    	$stmt->execute(array(':name' => $edu_desc));
	    	$institution_id = $pdo->lastInsertId();
	    }
	    $stmt= $pdo->prepare('UPDATE education SET profile_id = :prid, institution_id = :init, rank = :rank, year = :year');
	    $stmt->execute(array(
	    	':prid' => $profile_id,
	    	':inid' => $institution_id,
	    	':rank' => $rank,
	    	':year' => $year));
	    $rank++;
	}
}


function insertPosition($pdo, $profile_id) {
	$rank = 1;
	for($i=1; $i<=9; $i++) {
	    if ( ! isset($_POST['year'.$i]) ) continue;
	    if ( ! isset($_POST['desc'.$i]) ) continue;
	    $year = $_POST['year'.$i];
	    $desc = $_POST['desc'.$i];

	    $stmt = $pdo->prepare('INSERT INTO Position
	        (profile_id, rank, year, description)
	    VALUES ( :pid, :rank, :year, :desc)');
	    $stmt->execute(array(
	        ':pid' => $profile_id,
	        ':rank' => $rank,
	        ':year' => $year,
	        ':desc' => $desc)
	    );
	    $rank++;
	}
}

function insertEducation($pdo, $profile_id) {
	$rank = 1;
	for($i=1; $i<=9; $i++) {
	    if ( ! isset($_POST['edu_year'.$i]) ) continue;
	    if ( ! isset($_POST['edu_desc'.$i]) ) continue;
	    $year = $_POST['edu_year'.$i];
	    $edu_desc = $_POST['edu_desc'.$i];

	    // Lookup the school if it is there.
	    $institution_id =false;
	    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
	    $stmt->execute(array(':name' => $edu_desc));
	    $row = $stmt->fetch(PDO::FETCH_ASSOC);
	    if ($row !== false) $institution_id = $row['institution_id'];

	    // if there was no institution, insert it
	    if ($row === false) {
	    	$stmt = $pdo->prepare('INSERT INTO institution (name) VALUE (:name)');
	    	$stmt->execute(array(':name' => $edu_desc));
	    	$institution_id = $pdo->lastInsertId();
	    }
	    $stmt= $pdo->prepare('INSERT INTO education (profile_id, institution_id, rank, year) VALUES ( :prid, :inid, :rank, :year)');
	    $stmt->execute(array(
	    	':prid' => $profile_id,
	    	':inid' => $institution_id,
	    	':rank' => $rank,
	    	':year' => $year));
	    $rank++;
	}
}

?>
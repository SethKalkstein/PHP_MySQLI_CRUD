<?php
	//connect to database
	require ("config/dbconnect.php");
	//initialize input variables
	$petName = $petAge = $petSpecies = "";
	//initialize error message array
	$inputErrors = array("petName" => "", "petAge" => "", "petSpecies" => "");
	//acceptable characters
	$isValid = array("'", "-", ",", ".", " ");
	//check to see if submit has been pressed
	if(isset($_POST["submit"])){
		//check for empty input fields
		if(empty($_POST["petName"])){
			$inputErrors["petName"] = "a pet name is needed<br/>";
		} else {
			$petName = $_POST["petName"];
			//check that entry is valid
			if (!(ctype_alnum(str_replace($isValid, "", $petName)) && strlen($petName) <= 75)) {
				$inputErrors["petName"] = "Boo, $petName It not valid letters numbers or characters.";
			}
		}

		if(empty($_POST["petAge"])){
			$inputErrors["petAge"] = "a pet age is needed<br/>";
		} else {
			$petAge = $_POST["petAge"];
			if(!(ctype_digit(str_replace(".", "", $petAge)) && $petAge <= 507 && $petAge >= 0)) {
				 $inputErrors["petAge"] = "Boo, $petAge is not a valid number.";
			}
		}

		if(empty($_POST["petSpecies"])){
			$inputErrors["petSpecies"] = "a pet Species is needed<br/>";
		} else {
			$petSpecies = $_POST["petSpecies"];
			if (!(ctype_alnum(str_replace($isValid, "", $petSpecies)) && strlen($petSpecies) <= 50)){
				$inputErrors["petSpecies"] = "Boo, $petSpecies not valid letters, numbers or characters. ";
			}
		}
		//execute if input entry is successful
		if(!array_filter($inputErrors)){
			//sql injection protection
			$petName = mysqli_real_escape_string($conn, $_POST["petName"]);
			$petSpecies = mysqli_real_escape_string($conn, $_POST["petSpecies"]);
			$petAge = mysqli_real_escape_string($conn, $_POST["petAge"]);
			$petAge = (int) round($petAge);

			//assign a random human from the database (there's a HumanID foreign key, this will insure that it matches an existing one)
			$sqlHumans = "SELECT humanID FROM Human";
			$humanResult = mysqli_query($conn, $sqlHumans);
			$allHumans = mysqli_fetch_all($humanResult, MYSQLI_NUM);
			$randomHuman = $allHumans[rand(0,count($allHumans)-1)][0];

			//insert row into database
			$sqlPets = "INSERT INTO Pet(petAge, petSpecies, petName, humanID) Values($petAge, '$petSpecies', '$petName', '$randomHuman');";

			if(mysqli_query($conn, $sqlPets)){
			//clsoe database and redirect to homepage
				mysqli_close($conn);
				header("location: index.php");
			} else {
				//generate error message
				echo "BOO query error: " . mysqli_error($conn);
			}
		}
	} //end of post check
 ?>


<?php require("templates/header.php");?>

<section>
	<h2>Add a Pet</h2>
	<form action="add.php" method="POST">

		<label>Pet Name: </label>
		<input type="text" name="petName" value="<?php echo htmlspecialchars($petName); ?>">
		<p><?php echo $inputErrors["petName"]; ?></p>

		<label>Pet Species</label>
		<input type="text" name="petSpecies" value="<?php echo htmlspecialchars($petSpecies); ?>">
		<p><?php echo $inputErrors["petSpecies"]; ?></p>

		<label>Pet Age</label>
		<input type="text" name="petAge" value="<?php echo htmlspecialchars($petAge); ?>">
		<p><?php echo $inputErrors["petAge"]; ?></p>

		<input type="submit" name="submit" value="submit">

	</form>
</section>

<?php require("templates/footer.php");?>

<?php
	//connect to database
	require ("config/dbconnect.php");

	//checks for post request with delete
	if(isset($_POST["delete"])){
		$id_to_delete = mysqli_real_escape_string($conn, $_POST["id_to_delete"]);
		$sqlDelete = "DELETE FROM Pet WHERE petID = $id_to_delete";
		//redirects to index on delete
		if(mysqli_query($conn, $sqlDelete)){
			header("location: index.php");
		} else{
			echo "query error: ".mysqli_error($conn);
		}

	}

		//initialize error message array
		$inputErrors = array("petName" => "", "petAge" => "", "petSpecies" => "");

		//acceptable characters
		$isValid = array("'", "-", ",", ".", " ");

	//check to see if submit for edit has been pressed
	if(isset($_POST["submit"])){
		//get pet ID from URL
		$petID = $_GET['petID'];
		echo "<br/>Submit Edit Pet ID: ".$petID."<br/>";
		//get the human name associated with the pet
		$selectedHuman = $_POST["humanName"];
		echo "<br/> THis is the newly selected Human: ".$selectedHuman;

		//check for empy input fields
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
			$petID = (int) $petID;
			$humanName = mysqli_real_escape_string($conn, $_POST["humanName"]);

			//get the human selected from the drop down menu from human table (there's a HumanID foreign key, this will insure that it matches an existing one)
			$sqlHuman = "SELECT humanID FROM Human where humanName = '$humanName';";
			$humanResult = mysqli_query($conn, $sqlHuman);
			$oneHuman = mysqli_fetch_all($humanResult, MYSQLI_NUM);
			$oneHumanID = $oneHuman[0][0];

			//SQL code for update Pet table with data entered by use and humanID that corresponds to the human they chose ($oneHumanID)
			$sqlUpdate = "UPDATE Pet SET petAge = $petAge, petSpecies = '$petSpecies', petName = '$petName', humanID = $oneHumanID WHERE petID = $petID;";

			//execute query
			if(mysqli_query($conn, $sqlUpdate)){
			//stay on current page
				header("location: #");
			} else {
				//generate error message
				echo "BOO query error: " . mysqli_error($conn);
			}
		//if incorrect information was entered, allow edit feilds to remail visable
		} else {
			$isEditable = TRUE;
		}
	} //end of post check

//when user selects edit from the detail page
	if(isset($_POST["edit"])){
		//will set variable to be either edit or cancel
		$editOrCancel = $_POST["edit"];
		//
		$petID = mysqli_real_escape_string($conn, $_POST["id_to_update"]);

		//are the next two lines of code ever used?
		$isEditable = $_POST["is_editable"];
		$petIDPath = "$petID=".$petID;

		//check if user has selected to edit or cancel $isEditable will control whether or not an edit form will be visable in the HTML
		if($editOrCancel == "cancel"){
			$isEditable = FALSE;
		} else {
			$isEditable = TRUE;
		}
	}

	//check that GET request was received from sending page
	if(isset($_GET['petID'])){
		//grab the Primary Key (petID)
		$petID = mysqli_real_escape_string($conn, $_GET["petID"]);

		//make sql for Pet display
		$sqlJoinPetRow = "select Pet.petID, Pet.petName, Pet.petAge, Pet.petSpecies, Human.humanName from Human, Pet where Pet.humanID = Human.humanID and Pet.petID = $petID";

		//make SQL for list of humans dropdown
		$sqlHumanList = "select humanName from Human ORDER BY humanName;";

		//get the pet query result
		$result = mysqli_query($conn, $sqlJoinPetRow);

		//get the human query result
		$humanListResult = mysqli_query($conn, $sqlHumanList);

		//fetch the result in array format
		$pet = mysqli_fetch_assoc($result);

		//fetch all human names
		$humanList = mysqli_fetch_all($humanListResult, MYSQLI_NUM);

		//initialize edit form with data fetched in queries
		if(isset($_POST["edit"])) {
			$petName = $pet["petName"];
			$petSpecies = $pet["petSpecies"];
			$petAge = $pet["petAge"];
			$selectedHuman = $pet["humanName"];
		}

		//first time on the page set initialize editable to false
		if(!isset($_POST["edit"]) && !isset($_POST["submit"])){
			$isEditable = FALSE;
		}

			mysqli_free_result($result);
			mysqli_close($conn);
	}
	else {
		//error messages if GET attribute doesn't exist
		echo "NO GET";
		echo "upload problems as follows: " . mysqli_error($conn);
	}
 ?>

<?php require("templates/header.php");?>

	<h2>Details</h2>

	<div>
		<?php if($pet): ?>
			<h3><?php echo htmlspecialchars($pet[petName]); ?></h3>
			<ul>
				<?php foreach($pet as $meow => $purr): ?>
					<?php if($meow != "petID" && $meow != "petName"): ?>
						<li>
							<?php echo htmlspecialchars($meow) . ": " . htmlspecialchars($purr); ?>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<h3>Sorry :-( There is no record for this pet.</h3>
		<?php endif; ?>

		<!-- show the original above and the edit below: -->
		<h2>Edit Below</h2>

		<?php if($isEditable): ?>
			<!-- form for taking in new information -->
			<form action="details.php?petID=<?php echo $petID ?> " method="POST">
				<label>Pet Name: </label>
				<input type="text" name="petName" value="<?php echo htmlspecialchars($petName); ?>">
				<p><?php echo $inputErrors["petName"]; ?></p>

				<label>Pet Species</label>
				<input type="text" name="petSpecies" value="<?php echo htmlspecialchars($petSpecies); ?>">
				<p><?php echo $inputErrors["petSpecies"]; ?></p>

				<label>Pet Age</label>
				<input type="text" name="petAge" value="<?php echo htmlspecialchars($petAge); ?>">
				<p><?php echo $inputErrors["petAge"]; ?></p>

				<label>Human Name</label>
				 <select name="humanName">
				 	<?php  foreach($humanList as $h): ?>
				 		<option value="<?php echo $h[0]; ?>" <?php if($h[0] == $selectedHuman){echo "selected";} ?> > <?php echo $h[0] ?> </option>
				 	<?php endforeach; ?>
				 </select>
				<!-- submit the edit -->
				<input type="submit" name="submit" value="submit">
			</form>
			<!-- cancel button -->
			<form action="details.php?petID=<?php echo $petID ?> " method="POST">

				<input type="hidden" name="is_editable" value="<?php echo $isEditable ?>">
				<input type="submit" name="edit" value="cancel">

			</form>

		<?php else: ?>
		<!-- delete form -->
			<form action="details.php" method="POST">
				<input type="hidden" name="id_to_delete" value="<?php echo $pet['petID'] ?>">
				<input type="submit" name="delete" value="deletes">
			</form>
			<!-- edit form that can enable another form instead of the delete values on the same page? -->
			<form action="details.php?petID=<?php echo $petID ?> " method="POST">
				<input type="hidden" name="is_editable" value="<?php echo $isEditable ?>">
				<input type="submit" name="edit" value="edit">
			</form>

		<?php endif; ?>

	</div>

<?php require("templates/footer.php");?>

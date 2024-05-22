<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/hofin/config.php');
function connect()
{
	$mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
	// Error checker
	if ($mysqli->connect_errno != 0) {
		// error retriever
		$error = $mysqli->connect_error;
		// Date of error
		$error_date = date("F j, Y, g:i a");
		// Error message with date
		$message = "{$error} | {$error_date} \r\n";
		// Put the error in db-log.txt
		file_put_contents("db-log.txt", $message, FILE_APPEND);
		return false;
	} else {
		// Connection Successful
		$mysqli->set_charset("utf8mb4");
		return $mysqli;
	}
}
function getpic($id)
{
	$mysqli = connect();
	if (!$mysqli) {
		return false;
	}
	// Prepare and execute the SQL statement
	$stmt = $mysqli->prepare("SELECT face_pic FROM tbl_face WHERE user_id = ?");
	$stmt->bind_param('i', $id);
	$stmt->execute();
	// Get the result
	$result = $stmt->get_result();
	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_array();
		return $row['face_pic'];
	} else {
		return false;
	}
}
function userchecker($id)
{
	$mysqli = connect();
	if (!$mysqli) {
		return false;
	}
	// Prepare and execute the SQL statement
	$stmt1 = $mysqli->prepare("SELECT is_admin, is_deleted, is_verified FROM tbl_useracc WHERE user_id = ?");
	$stmt1->bind_param('i', $id);
	$stmt1->execute();
	// Get the result
	$result1 = $stmt1->get_result();
	// Check if there are results and fetch the data
	if ($result1 && $result1->num_rows > 0) {
		$row1 = $result1->fetch_array();
		// Prepare and execute the SQL statement
		$stmt2 = $mysqli->prepare("SELECT given_name, last_name	FROM tbl_userinfo WHERE user_id = ?");
		$stmt2->bind_param('i', $id);
		$stmt2->execute();
		// Get the result
		$result2 = $stmt2->get_result();
		$row2 = $result2->fetch_array();
		$is_admin = $row1['is_admin'];
		$is_deleted = $row1['is_deleted'];
		$is_verified = $row1['is_verified'];
		$name = $row2['given_name'] . ' ' . $row2['last_name'];
		if (isset($is_deleted) && $is_deleted == "no") {
			if (isset($is_verified) && $is_verified == "yes") {
				if (isset($is_admin) && $is_admin == "yes") {
					$_SESSION['user_id'] = $id;
					$_SESSION['is_admin'] = 'yes';
					$_SESSION['usertype'] = 'ADMIN';
					$_SESSION['fullname'] = $name;
					echo "  <script>
						// Simulate loading delay
						setTimeout(function() {
						// Redirect to another page after 3 seconds
						window.location.href = './admin/dashboard.php';
						}, 1500); // 2000 milliseconds = 3 seconds
					</script>";
					return 'success';
				} else {
					$_SESSION['user_id'] = $id;
					$_SESSION['is_admin'] = 'no';
					$_SESSION['usertype'] = 'USER';
					$_SESSION['fullname'] = $name;
					echo "  <script>
						// Simulate loading delay
						setTimeout(function() {
						// Redirect to another page after 3 seconds
						window.location.href = './member/dashboard.php';
						}, 1500); // 2000 milliseconds = 3 seconds
					</script>";
					return 'success';
				}
			} else {
				return 'Account is not yet verified, please wait for the admin to verify your account.';
			}
		} else {
			return 'Account was deleted, if you think this is a mistake please contact an admin.';
		}
	}
}
function create_user()
{
	$mysqli = connect();
	if (!$mysqli) {
		return false;
	}
	// Get the last user ID
	$result = mysqli_query($mysqli, "SELECT user_id FROM tbl_useracc ORDER BY user_id DESC LIMIT 1");
	// Check if the query returned any rows
	if ($result && mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result);
		$lastId = $row['user_id'];
	} else {
		// If no rows returned, set lastId to 0
		$lastId = 0;
	}
	// Generate the username
	$username = date('Y') . sprintf('%04d', $lastId + 1);
	// Close the database connection
	mysqli_close($mysqli);
	return $username;
}
function register($given_name, $middle_name, $last_name, $gender, $bday, $username, $password, $confirm, $email, $image_data)
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
	if (!$mysqli) {
		return false;
	}
	// Check if the passwords match
	if ($password !== $confirm) {
		return "Passwords do not match.";
	}
	// Check if the password is too long.
	if (strlen($password) < 8) {
		// If password is too long, return an error message.
		return "Password is too short, must be 8-24 characters";
	}
	if (strlen($password) > 24) {
		// If password is too long, return an error message.
		return "Password is too long, must be 8-24 characters";
	}
	if (isset($given_name) && isset($middle_name) && isset($last_name) && isset($gender) && isset($bday) && isset($username) && isset($password) && isset($confirm) && isset($email) && isset($image_data)) {
		// Check if the fields are not empty
		if (!empty($given_name) && !empty($middle_name) && !empty($last_name) && !empty($gender) && !empty($bday) && !empty($username) && !empty($password) && !empty($image_data)) {
			// Function to create a folder
			$folderName = $given_name . ' ' . $middle_name . ' ' . $last_name;
			function createFolder($folderName)
			{
				// Specify the directory where the folder will be created
				$directory = "face/labels/";
				// Check if the folder already exists
				if (!is_dir($directory . $folderName)) {
					// Create the folder
					if (mkdir($directory . $folderName, 0777, true)) {
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			}
			// Create folder if it doesn't exist
			if (!createFolder($folderName)) {
				echo '<div class="alert alert-danger" role="alert">Failed to create folder.</div>';
				exit;
			}
			// Save the images
			foreach ($image_data as $index => $imageData) {
				$imagePath = "face/labels/$folderName/" . ($index) . ".jpg";
				$folderpath = "face/labels/$folderName/";
				if (!file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)))) {
					echo '<div class="alert alert-danger" role="alert">Failed to save image $index.</div>';
					exit;
				}
			}
			// Prepare data to save to labels.json
			$dataToSave = array(
				'name' => $given_name . ' ' . $middle_name . ' ' . $last_name,
				'gender' => $gender,
				'bday' => $bday,
				'username' => $username,
				'password' => $password,
			);
			// Read existing data from labels.json
			$labelsFilePath = './face/labels.json';
			$existingData = array();
			if (file_exists($labelsFilePath)) {
				$encryptedDataWithIV = file_get_contents($labelsFilePath);
				if ($encryptedDataWithIV !== false) {
					$iv_hex = substr($encryptedDataWithIV, 0, 32); // Extract IV from the beginning
					$encryptedData = substr($encryptedDataWithIV, 32); // Extract encrypted data without IV
					$decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', 'Adm1n123', 0, hex2bin($iv_hex));
					if ($decryptedData !== false) {
						$existingData = json_decode($decryptedData, true);
					} else {
						return 'Failed to decrypt data from labels.json.';
					}
				} else {
					return 'Failed to read data from labels.json.';
				}
			}
			// Check if the name already exists
			$nameExists = false;
			foreach ($existingData as $data) {
				if (
					$data['name'] == $dataToSave['name'] &&
					$data['gender'] == $dataToSave['gender'] &&
					$data['bday'] == $dataToSave['bday']
				) {
					$nameExists = true;
					break;
				}
			}
			if ($nameExists) {
				return 'Name already exists in the database.';
			}
			// Append new data to existing data
			$existingData[] = $dataToSave;
			// Encrypt and write updated data back to labels.json
			$iv = openssl_random_pseudo_bytes(16); // Generate a random IV of 16 bytes (128 bits)
			$iv_hex = bin2hex($iv); // Convert the binary IV to hexadecimal representation
			$encryptedData = openssl_encrypt(json_encode($existingData), 'aes-256-cbc', 'Adm1n123', 0, $iv);
			$encryptedDataWithIV = $iv_hex . $encryptedData; // Combine IV and encrypted data
			if (file_put_contents($labelsFilePath, $encryptedDataWithIV)) {
				echo '<script>console.log("Data saved successfully.");</script>';
				$hashed_password = password_hash($password, PASSWORD_DEFAULT);
				// Prepare the SQL query
				$is_admin = "no";
				$is_deleted = "no";
				$is_verified = "no";
				$stmt1 = $mysqli->prepare("INSERT INTO tbl_useracc (username, password, email, is_admin, is_deleted, is_verified) VALUES (?, ?, ?, ?, ?, ?)");
				$stmt1->bind_param("ssssss", $username, $hashed_password, $email, $is_admin, $is_deleted, $is_verified);
				// Execute the query
				if ($stmt1->execute()) {
					$user_id = $mysqli->insert_id;
					$stmt2 = $mysqli->prepare("INSERT INTO tbl_userinfo (user_id, given_name, middle_name, last_name, gender, bday) VALUES (?, ?, ?, ?, ?, ?)");
					$stmt2->bind_param("isssss", $user_id, $given_name, $middle_name, $last_name, $gender, $bday);
					if ($stmt2->execute()) {
						$stmt3 = $mysqli->prepare("INSERT INTO tbl_face (user_id, face_pic) VALUES (?, ?)");
						$stmt3->bind_param("is", $user_id, $folderpath);
						if ($stmt3->execute()) {
							// Close the statement and connection
							$stmt1->close();
							$stmt2->close();
							$stmt3->close();
							echo "  <script>
								setTimeout(function() {
								window.location.href = './index.php';
								}, 3000); // 3000 milliseconds = 3 seconds
							</script>";
							return "success";
						} else {	// If there's an error, log it
							$error = $stmt1->error;
							$error_date = date("F j, Y, g:i a");
							$message = "{$error} | {$error_date} \r\n";
							file_put_contents("db-log.txt", $message, FILE_APPEND);
							// Close the statement and connection
							$stmt1->close();
							$mysqli->close();
							return "Registration failed.";
						}
					} else {	// If there's an error, log it
						$error = $stmt1->error;
						$error_date = date("F j, Y, g:i a");
						$message = "{$error} | {$error_date} \r\n";
						file_put_contents("db-log.txt", $message, FILE_APPEND);
						// Close the statement and connection
						$stmt1->close();
						$mysqli->close();
						return "Registration failed.";
					}
				} else {
					// If there's an error, log it
					$error = $stmt1->error;
					$error_date = date("F j, Y, g:i a");
					$message = "{$error} | {$error_date} \r\n";
					file_put_contents("db-log.txt", $message, FILE_APPEND);
					// Close the statement and connection
					$stmt1->close();
					$mysqli->close();
					return "Registration failed.";
				}
			} else {
				echo '<script>console.log("Failed to save data to labels.json.");</script>';
			}
		} else {
			// Return error message if required fields are empty
			return 'There are missing fields that are required';
		}
	} else {
		// Return error message if required keys are not set
		return 'All fields are required.';
	}
	// Hash the password
}
function login($username, $password)
{
	$mysqli = connect();
	if (!$mysqli) {
		return "Database connection error";
	}
	$username = trim($username);
	$password = trim($password);
	if ($username == "" || $password == "") {
		return "Both fields are required";
	}
	$username = filter_var($username, FILTER_SANITIZE_STRING);
	$password = filter_var($password, FILTER_SANITIZE_STRING);
	$sql = "SELECT username, password, user_id FROM tbl_useracc WHERE username = ?";
	$stmt = $mysqli->prepare($sql);
	if (!$stmt) {
		return "Database error: " . $mysqli->error;
	}
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$data = $result->fetch_assoc();
	$max_attempts = 2;
	$lockout_time = 300; // 5 minutes in seconds


	if (isset($_SESSION['last_attempt']) && $_SESSION['last_attempt'] !== null) {
		$remaining_time = $lockout_time - (time() - $_SESSION['last_attempt']);
		if ($remaining_time > 0) {
			return "Account is locked. Please try again in $remaining_time seconds.";
		} else {
			$_SESSION['last_attempt'] = null; // Reset lockout
		}
	}



	if (!isset($_SESSION['login_attempts'])) {
		$_SESSION['login_attempts'] = 1;
	} else {
		$_SESSION['login_attempts']++;
	}

	if ($_SESSION['login_attempts'] > $max_attempts) {
		if (!isset($_SESSION['last_attempt'])) {
			$_SESSION['last_attempt'] = time();
		}
	}

	if (
		$_SESSION['login_attempts'] >= 3 &&
		(time() - $_SESSION['last_attempt']) > $lockout_time
	) {
		$_SESSION['login_attempts'] = 1;
		$_SESSION['last_attempt'] = null;
	}

	if (
		$_SESSION['login_attempts'] > $max_attempts &&
		(time() - $_SESSION['last_attempt']) < $lockout_time
	) {
		// Account is locked
		$_SESSION['login_attempts'] = 3;
		$remaining_time = $lockout_time - (time() - $_SESSION['last_attempt']);
		return "Account is locked. Please try again in <span id='remainingTime'>$remaining_time</span> seconds.";
	}
	if ($data == NULL || password_verify($password, $data["password"]) == false) {
		if ($_SESSION['login_attempts'] >= 3) {
			$_SESSION['last_attempt'] = time();
		}
		return "Wrong username or password";
	} else {
		unset($_SESSION['login_attempts']);
		unset($_SESSION['last_attempt']);
		$id = $data["user_id"];
		return userchecker($id);
	}
}




function update_password($given_name, $middle_name, $last_name, $gender, $bday, $username, $password)
{
	if (isset($given_name) && isset($middle_name) && isset($last_name) && isset($gender) && isset($bday) && isset($username) && isset($password)) {
		// Prepare data to save to labels.json
		$dataToSave = array(
			'name' => $given_name . ' ' . $middle_name . ' ' . $last_name,
			'username' => $username,
			'password' => $password,
			'gender' => $gender,
			'bday' => $bday,
		);
		// Read existing data from labels.json
		$labelsFilePath = './face/labels.json';
		$existingData = array();
		if (file_exists($labelsFilePath)) {
			$encryptedDataWithIV = file_get_contents($labelsFilePath);
			if ($encryptedDataWithIV !== false) {
				$iv_hex = substr($encryptedDataWithIV, 0, 32); // Extract IV from the beginning
				$encryptedData = substr($encryptedDataWithIV, 32); // Extract encrypted data without IV
				$decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', 'Adm1n123', 0, hex2bin($iv_hex));
				if ($decryptedData !== false) {
					$existingData = json_decode($decryptedData, true);
				} else {
					return 'Failed to decrypt data from labels.json.';
				}
			} else {
				return 'Failed to read data from labels.json.';
			}
		}
		// Check if the username already exists and update the data if it does
		$userExists = false;
		foreach ($existingData as &$data) {
			if (
				$data['name'] == $dataToSave['name'] &&
				$data['gender'] == $dataToSave['gender'] &&
				$data['bday'] == $dataToSave['bday']
			) {
				$data = $dataToSave; // Update existing user data
				$userExists = true;
				break;
			}
		}
		// If the user does not exist, append new data
		if (!$userExists) {
			$existingData[] = $dataToSave;
		}
		// Encrypt and write updated data back to labels.json
		$iv = openssl_random_pseudo_bytes(16); // Generate a random IV of 16 bytes (128 bits)
		$iv_hex = bin2hex($iv); // Convert the binary IV to hexadecimal representation
		$encryptedData = openssl_encrypt(json_encode($existingData), 'aes-256-cbc', 'Adm1n123', 0, $iv);
		$encryptedDataWithIV = $iv_hex . $encryptedData; // Combine IV and encrypted data
		if (file_put_contents($labelsFilePath, $encryptedDataWithIV)) {
			return "success";
		} else {
			return "Failed to save data to labels.json.";
		}
	} else {
		return 'All fields are required.';
	}
}



//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++   ADMIN   ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function edituser()
{
	if (isset($_POST['approvemember'])) {
		$mysqli = connect();
		$stmt = $mysqli->prepare("UPDATE tbl_useracc SET is_verified = 'yes' WHERE user_id = ?");
		$stmt->bind_param('i', $_GET['id']);
		$stmt->execute();
		header('Location: members_info.php');
		exit;
	}

	if (isset($_POST['declinemember'])) {
		$mysqli = connect();
		$stmt = $mysqli->prepare("DELETE FROM tbl_useracc WHERE user_id = ?");
		$stmt->bind_param('i', $_GET['id']);
		$stmt->execute();
		header('Location: members_info.php');
		exit;
	}
}


function getinfo()
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in the database connection, the function will stop.
	if (!$mysqli) {
		return false;
	}

	// Fetch unverified members
	$unvQuery = "
        SELECT *
        FROM tbl_useracc, tbl_userinfo, tbl_face
        WHERE tbl_useracc.user_id = tbl_userinfo.user_id
			AND tbl_useracc.user_id = tbl_face.user_id
            AND tbl_useracc.is_verified = 'no'
            AND tbl_useracc.is_admin = 'no'
            AND tbl_useracc.is_deleted = 'no'
        ORDER BY tbl_userinfo.last_name ASC;
    ";
	$unvResult = $mysqli->query($unvQuery);
	$unv = $unvResult->fetch_all(MYSQLI_ASSOC);

	// Fetch incomplete property info
	$incQuery = "
        SELECT *
        FROM tbl_property
        JOIN tbl_userinfo ON tbl_property.user_id = tbl_userinfo.user_id
        JOIN tbl_useracc ON tbl_property.user_id = tbl_useracc.user_id
        WHERE is_admin = 'no' 
            AND is_deleted = 'no'
            AND is_verified = 'yes'
            AND (blk_no IS NULL 
            OR lot_no IS NULL 
            OR homelot_area IS NULL 
            OR open_space IS NULL 
            OR sharein_loan IS NULL 
            OR principal_interest IS NULL 
            OR MRI IS NULL 
            OR total IS NULL)
    ";
	$incResult = $mysqli->query($incQuery);
	$inc = $incResult->fetch_all(MYSQLI_ASSOC);


	// Fetch incomplete property info
	$compQuery = "
        SELECT *
        FROM tbl_property
        JOIN tbl_userinfo ON tbl_property.user_id = tbl_userinfo.user_id
        JOIN tbl_useracc ON tbl_property.user_id = tbl_useracc.user_id
        WHERE is_admin = 'no' 
            AND is_deleted = 'no'
            AND is_verified = 'yes'
            AND (blk_no IS NULL 
            OR lot_no IS NULL 
            OR homelot_area IS NULL 
            OR open_space IS NULL 
            OR sharein_loan IS NULL 
            OR principal_interest IS NULL 
            OR MRI IS NULL 
            OR total IS NULL)
    ";
	$compResult = $mysqli->query($compQuery);
	$comp = $compResult->fetch_all(MYSQLI_ASSOC);



	return [
		'unverified_members' => $unv,
		'incomplete_members' => $inc,
		'completed_members' => $comp
	];
}

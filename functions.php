<?php
require (__DIR__ . '/config.php');

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

function create_user()
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
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


function register($given_name, $middle_name, $last_name, $username, $password, $confirm, $email, $image_data)
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


	if (isset($given_name) && isset($middle_name) && isset($last_name) && isset($username) && isset($password) && isset($confirm) && isset($email) && isset($image_data)) {

		// Check if the fields are not empty
		if (!empty($given_name) && !empty($middle_name) && !empty($last_name) && !empty($username) && !empty($password) && !empty($image_data)) {
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
				if (!file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)))) {
					echo '<div class="alert alert-danger" role="alert">Failed to save image $index.</div>';
					exit;
				}
			}

			// Prepare data to save to labels.json
			$dataToSave = array(
				'name' => $given_name . ' ' . $middle_name . ' ' . $last_name,
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
				if ($data['name'] == $dataToSave['name']) {
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

				$stmt = $mysqli->prepare("INSERT INTO tbl_useracc (username, password, email, is_admin, is_deleted, is_verified) VALUES (?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("ssssss", $username, $hashed_password, $email, $is_admin, $is_deleted, $is_verified);

				// Execute the query
				if ($stmt->execute()) {
					// Close the statement and connection
					$stmt->close();
					$mysqli->close();
					return "success";
				} else {
					// If there's an error, log it
					$error = $stmt->error;
					$error_date = date("F j, Y, g:i a");
					$message = "{$error} | {$error_date} \r\n";
					file_put_contents("db-log.txt", $message, FILE_APPEND);

					// Close the statement and connection
					$stmt->close();
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
	$lockout_time = 300; //300 = 5 minutes in seconds, 60 = 1 minute

	if (!isset($_SESSION['login_attempts'])) {
		$_SESSION['login_attempts'] = [];
	}

	if (!isset($_SESSION['last_attempt'])) {
		$_SESSION['last_attempt'] = [];
	}

	if (!isset($_SESSION['login_attempts'][$username])) {
		$_SESSION['login_attempts'][$username] = 1;
	} else {
		$_SESSION['login_attempts'][$username]++;
	}


	if (
		$_SESSION['login_attempts'][$username] > $max_attempts
	) {
		if (!isset($_SESSION['last_attempt'][$username])) {
			$_SESSION['last_attempt'][$username] = time();
		}
	}

	if (
		$_SESSION['login_attempts'][$username] >= 3 && (time() - $_SESSION['last_attempt'][$username]) > $lockout_time
	) {
		$_SESSION['login_attempts'][$username] = 1;
		$_SESSION['last_attempt'][$username] = null;
	}

	if (
		$_SESSION['login_attempts'][$username] > $max_attempts &&
		(time() - $_SESSION['last_attempt'][$username]) < $lockout_time
	) {
		// Account is locked
		$_SESSION['login_attempts'][$username] = 3;
		$remaining_time = $lockout_time - (time() - $_SESSION['last_attempt'][$username]);
		return "Account is locked. Please try again in <span id='remainingTime'>$remaining_time</span> seconds.";
	}

	if ($data == NULL || password_verify($password, $data["password"]) == false) {

		return "Wrong username or password";
	} else {
		unset($_SESSION['login_attempts'][$username]);
		unset($_SESSION['last_attempt'][$username]);

		$id = $data["user_id"];
		$_SESSION["id"] = $id;
		return 'success';
	}
}


function update_password($given_name, $middle_name, $last_name, $username, $password)
{

	// Prepare data to save to labels.json
	$dataToSave = array(
		'name' => $given_name . ' ' . $middle_name . ' ' . $last_name,
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

	// Check if the username already exists and update the data if it does
	$userExists = false;
	foreach ($existingData as &$data) {
		if ($data['username'] == $dataToSave['username']) {
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
}

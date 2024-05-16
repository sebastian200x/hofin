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


function register($username, $password, $confirm_password, $email)
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
	if (!$mysqli) {
		return false;
	}

	// Check if the passwords match
	if ($password !== $confirm_password) {
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


	// Hash the password
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

	if ($data == NULL) {
		return "Wrong username or password";
	}

	$max_attempts = 3;
	$lockout_time = 300; // 5 minutes in seconds

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
		$_SESSION['login_attempts'][$username] > $max_attempts &&
		(time() - $_SESSION['last_attempt'][$username]) < $lockout_time
	) {
		// Account is locked
		$remaining_time = $lockout_time - (time() - $_SESSION['last_attempt'][$username]);
		return "Account is locked. Please try again in $remaining_time seconds.";
	}

	if (password_verify($password, $data["password"]) == false) {
		if (!isset($_SESSION['last_attempt'][$username])) {
			$_SESSION['last_attempt'][$username] = time();
		}
		return "Wrong username or password";
	} else {
		$_SESSION['login_attempts'][$username] = 0;
		$_SESSION['last_attempt'][$username] = null;

		unset($_SESSION['login_attempts'][$username]);
		unset($_SESSION['last_attempt'][$username]);

		$id = $data["user_id"];
		$_SESSION["admin"] = $id;
		return 'success';
	}
}

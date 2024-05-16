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

	// Hash the password
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);

	// Prepare the SQL query

	$is_admin = "no";
	$is_deleted = "no";
	$is_verified = "no";

	$stmt = $mysqli->prepare("INSERT INTO tbl_useracc (username, password, email, is_admin, is_deleted, is_verified) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssss", $username, $password, $email, $is_admin, $is_deleted, $is_verified);

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
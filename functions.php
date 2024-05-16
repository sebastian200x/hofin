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


function register()
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
	if (!$mysqli) {
		return false;
	}

	$first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
	$middle_name = trim(filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_STRING));
	$last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));

	$gender = trim(filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING));
	$bday = trim(filter_input(INPUT_POST, 'bday', FILTER_SANITIZE_STRING));

	$password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
	$confirm_password = trim(filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_STRING));
	$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));


	$args = func_get_args();
	foreach ($args as $value) {
		if (empty($value)) {
			// If any field is empty, return an error message.
			return "All fields are required";
		}
	}

	// Check for disallowed characters (< and >).
	foreach ($args as $value) {
		if (preg_match("/([<|>])/", $value)) {
			// If disallowed characters are found, 
			// return an error message.
			return "< and > characters are not allowed";
		}
	}

	

}
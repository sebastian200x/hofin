<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="./styles/css/register.css">
    <?php include 'navbar.php'; ?>
</head>

<body style="margin-top:100px">

    <div class="left">
        <video id="video" width="100%" height="auto" autoplay></video>
        <small>
            <center> Take a picture 3 times before you click save</center>
        </small><br>
        <button id="capture-btn" class="login">Capture</button>
    </div>
    <div class="right">
        <div class="centered">
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                // Check if the required fields are set
                if (isset($_POST['first_name']) && isset($_POST['middle_name']) && isset($_POST['last_name']) && isset($_POST['gender']) && isset($_POST['bday']) && isset($_POST['password']) && isset($_POST['confirm']) && isset($_POST['email']) && isset($_POST['image-data'])) {
                    // Get user input
                    $first_name = $_POST['first_name'];
                    $middle_name = $_POST['middle_name'];
                    $last_name = $_POST['last_name'];

                    $gender = $_POST['gender'];
                    $bday = $_POST['bday'];

                    $password = $_POST['password'];
                    $confirm_password = $_POST['confirm'];
                    $email = $_POST['email'];


                    $folderName = $first_name . ' ' . $last_name;
                    $imageDataList = json_decode($_POST['image-data']);
                    if ($password == $confirm) {

                    }

                    // Check if the fields are not empty
                    if (!empty($first_name) && !empty($last_name) && !empty($imageDataList)) {
                        // Function to create a folder
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
                        foreach ($imageDataList as $index => $imageData) {
                            $imagePath = "face/labels/$folderName/" . ($index) . ".jpg";
                            if (!file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)))) {
                                echo '<div class="alert alert-danger" role="alert">Failed to save image $index.</div>';
                            echo '<header class="error"> &#10060 Failed to save image $index.</header>';
                            exit;
                            }
                        }

                        // Prepare data to save to labels.json
                        $dataToSave = array(
                            'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
                            'gender' => $gender,
                            'bday' => $bday
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
                            echo '<header class="error"> &#10060 Failed to decrypt data from labels.json.</header>';
                            exit;
                                }
                            } else {
                            echo '<header class="error"> &#10060 Failed to read data from labels.json.</header>';
                            exit;
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
                            echo '<header class="error"> &#10060 Name already exists in the database.</header>';
                            exit;
                        }

                        // Append new data to existing data
                        $existingData[] = $dataToSave;

                        // Encrypt and write updated data back to labels.json
                        $iv = openssl_random_pseudo_bytes(16); // Generate a random IV of 16 bytes (128 bits)
                        $iv_hex = bin2hex($iv); // Convert the binary IV to hexadecimal representation
                        $encryptedData = openssl_encrypt(json_encode($existingData), 'aes-256-cbc', 'Adm1n123', 0, $iv);
                        $encryptedDataWithIV = $iv_hex . $encryptedData; // Combine IV and encrypted data
            
                        if (file_put_contents($labelsFilePath, $encryptedDataWithIV)) {
                            echo '<header class="error"> &#10060 Data saved successfully.</header>';
                        } else {
                            echo '<header class="error"> &#10060 Failed to save data to labels.json.</header>';
                        }

                        // Redirect to index page after 2 seconds
                        echo '<script>
                setTimeout(function() {
                    window.location.href = "?page=face";
                }, 2000);
            </script>';
                        exit; // Stop further execution
                    } else {
                        // Return error message if required fields are empty
                        echo '<header class="error"> &#10060 Required fields are empty.</header>';
                        exit;
                    }
                } else {
                    // Return error message if required keys are not set
                    echo '<header class="error"> &#10060 Required keys are not set.</header>';
                    exit;
                }
            }
            ?>
            <br>

            <form action="/register" method="POST">
                <h1 class="reg">Register Account</h1>
                <div class="personal-information">
                    <h3 class="info">Personal Information</h3>
                    <br>
                    <div class="personal-info">
                        <div class="personal-info-col"><label for="given-name">Given Name</label></div>
                        <div class="personal-info-col"><label for="middle-name">Middle Name</label></div>
                        <div class="personal-info-col"><label for="last-name">Last Name</label></div>
                        <div class="personal-info-col"><input type="text" name="given_name" required autofocus
                                placeholder="First Name"></div>
                        <div class="personal-info-col"><input type="text" name="middle_name" required
                                placeholder="Middle Name"></div>
                        <div class="personal-info-col-last"><input type="text" name="last_name" required
                                placeholder="Last Name"></div>
                    </div>
                    <div class="double-half">
                        <div class="personal-half">
                            <label for="gender">Gender
                                <select name="gender" name="gender" required>
                                    <option value="" selected hidden>Please select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </label>
                        </div>
                        <div class="personal-half">
                            <label for="birthday">Birthday
                                <input type="date" name="bday" max="<?php echo date('Y-m-d'); ?>" id="bday" required>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="account-information">
                    <br>
                    <h3 class="info">Account Information</h3>

                    <label for="username">Username</label>
                    <input type="text" name="username" value="{{ username }}" required disabled>
                    <p class="note">Please don't forget this, This will be your permanent username</p>

                    <div class="account-info">
                        <div class="account-info-col"><label for="password">Password</label></div>
                        <div class="account-info-col"><label for="confirm-password">Confirm Password</label></div>
                        <div class="account-info-col">
                            <input type="password" name="password" required placeholder="Password"
                                pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}"
                                title="Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol, and be at least 8 characters long"
                                class="{% if (messager == 1 )%}wrong{% endif %}">

                        </div>
                        <div class="account-info-col-last">
                            <input type="password" name="confirm" required placeholder="Confirm Password"
                                class="{% if (messager == 1 )%}wrong{% endif %}"
                                pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}"
                                title="Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol, and be at least 8 characters long">
                        </div>
                    </div>

                    <label for="email">Email
                        <input type="email" name="email" placeholder="Email" required>
                    </label>

                    <!-- <button class="button" id="hoverinbtn" type="submit">Register</button> -->
                    <input type="submit" class="login" value="Register">
                    <a href="/login" class="login">Back</a>

            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        const video = document.getElementById('video');
        const captureBtn = document.getElementById('capture-btn');
        const imageDataInput = document.getElementById('image-data');
        let captureCount = 1;
        let capturedImages = [];

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => {
                console.error('Error accessing camera:', error);
            });

        captureBtn.addEventListener('click', () => {
            if (captureCount < 3) {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = canvas.toDataURL('image/jpeg');
                capturedImages.push(imageData);
                alert('Image captured ' + captureCount);
                if (captureCount === 3) {
                    captureBtn.disabled = true;
                }
            } else {
                alert('You have captured the maximum number of photos.');
            }
            captureCount++;
        });

        document.getElementById('info-form').addEventListener('submit', event => {
            event.preventDefault();
            imageDataInput.value = JSON.stringify(capturedImages);
            document.getElementById('info-form').submit(); // Submit the form
        });
    </script>



</html>
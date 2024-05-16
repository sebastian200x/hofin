<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="./styles/css/register.css">
    <?php include 'navbar.php'; ?>
</head>

<body>
    <div class="left">
        <video id="video" width="100%" height="auto" autoplay></video>
        <p class="note">Take a picture 3 times before you click save</p>
        <br>
        <button id="capture-btn" class="login">Capture</button>
    </div>
    <div class="right">
        <div class="centered">
            <?php
            if (isset($_POST['submit'])) {
                $given_name = $_POST['given_name'];
                $middle_name = $_POST['middle_name'];
                $last_name = $_POST['last_name'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm'];
                $email = $_POST['email'];
                $image_data = $_POST['image_data'];

                $response = register($username, $password, $confirm_password, $email);
                echo $response;
            }
            ?>
            <br>
            <form action="" method="POST" enctype="multipart/form-data" id="info-form">
                <h1 class="reg">Register Account</h1>
                <div class="personal-information">
                    <h3 class="info">Personal Information</h3>
                    <br>
                    <div class="personal-info">
                        <div class="personal-info-col"><label for="given_name">Given Name</label></div>
                        <div class="personal-info-col"><label for="middle_name">Middle Name</label></div>
                        <div class="personal-info-col"><label for="last_name">Last Name</label></div>
                        <div class="personal-info-col"><input type="text" id="given_name" name="given_name" required
                                autofocus placeholder="First Name"></div>
                        <div class="personal-info-col"><input type="text" id="middle_name" name="middle_name" required
                                placeholder="Middle Name"></div>
                        <div class="personal-info-col-last"><input type="text" id="last_name" name="last_name" required
                                placeholder="Last Name"></div>
                    </div>
                    <div class="double-half">
                        <div class="personal-half">
                            <label for="gender">Gender
                                <select id="gender" name="gender" required>
                                    <option value="" selected hidden>Please select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </label>
                        </div>
                        <div class="personal-half">
                            <label for="bday">Birthday
                                <input type="date" id="bday" name="bday" max="<?php echo date('Y-m-d'); ?>" required>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="account-information">
                    <br>
                    <h3 class="info">Account Information</h3>
                    <label for="username">Username</label>
                    <?php $user = create_user(); ?>
                    <input type="text" id="username" name="username" value="<?php echo $user; ?>" required readonly>
                    <p class="note">Please don't forget this, This will be your permanent username</p>
                    <div class="account-info">
                        <div class="account-info-col"><label for="password">Password</label></div>
                        <div class="account-info-col"><label for="confirm">Confirm Password</label></div>
                        <div class="account-info-col">
                            <input type="password" id="password" name="password" required placeholder="Password"
                                pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}"
                                title="Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol, and be at least 8 characters long">
                        </div>
                        <div class="account-info-col-last">
                            <input type="password" id="confirm" name="confirm" required placeholder="Confirm Password"
                                pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}"
                                title="Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol, and be at least 8 characters long">
                        </div>
                    </div>
                    <label for="email">Email
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </label>
                    <input type="hidden" id="image-data" name="image_data">
                    <input type="submit" class="login" name="submit" value="Register">
                    <a href="./index.php" class="login">Back</a>
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
        let captureCount = 0;
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
                alert('Image captured ' + (captureCount + 1));
                captureCount++;
                if (captureCount === 3) {
                    captureBtn.disabled = true;
                }
            } else {
                alert('You have captured the maximum number of photos.');
            }
        });

        document.getElementById('info-form').addEventListener('submit', event => {
            event.preventDefault();
            imageDataInput.value = JSON.stringify(capturedImages);
            event.target.submit(); // Submit the form
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./styles/css/index.css">
    <?php
    include 'navbar.php';
    include 'functions.php';
    ?>
</head>

<body>

    <div class="left"><img draggable="false" class="image" src="./styles/images/logo-b.png" alt="logo">
    </div>
    <div class="right">
        <div class="centered">
            <!-- <header class="inv" style="color:white;box-shadow: 5px 7px 15px black;border: 2px solid red;border-radius: 50px;background-color: red;width:200px">
                        &#9997 Please enter your username
                    </header> -->
            <!-- <header style="color:white;box-shadow: 5px 7px 15px black;border: 2px solid red;border-radius: 50px;background-color: red;width:300px ">
                        &#10060 Invalid Username or Password
                    </header> -->
            <!-- <header style="color:white;box-shadow: 5px 7px 15px black;border: 2px solid red;border-radius: 50px;background-color: red;width:200px">
                        &#9997 Please enter your password
                    </header> -->
            <form action="#" method="POST" autocomplete="off">
                <div class="imgcontainer">
                    <img draggable="false" src="./styles/images/logo-s.png" alt="Avatar" class="avatar">
                    <h1 style="color: #545454;">LOGIN</h1>
                </div>
                <br><br>
                <label for="uname">Username:</label>

                <input type="text" name="username" id="uname" pattern="^\S{3,}$"
                    title="Username must have 3 characters and cannot have spaces" placeholder="Enter Username" required
                    autocomplete="false" autofocus>
                <br><br>

                <label for="psw">Password:</label>
                <input type="password" name="password" id="psw" pattern="^\S{3,}$"
                    title="Password must have 3 characters and cannot have spaces" placeholder="Enter Password" required
                    autocomplete="false">
                <input class="button" type="submit" value="Login" name="login">
                <a href="/facelogin" class="face-password-link">Face Login</a>
                <a href="./register.php" class="face-password-link">Register</a>
            </form>

        </div>
    </div>
</body>

</html>
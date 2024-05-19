<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/hofin/styles/css/member/home.css">
    <?php require ($_SERVER['DOCUMENT_ROOT'] . '/hofin/navbar.php'); ?>
</head>

<body style="padding-top: 100px;">

    <h1 class="center" style="padding-top: 20px;">Members Information</h1>

    <div class="container">
        <div class="card">
            <h2>Pending Bills</h2>
            <p>Total Unpaid Bills: </p>
            <a href="/members/payment" class="button">Pay</a>
        </div>
        <div class="card">
            <h2>Verified Payments</h2>
            <p>Total transactions: </p>
            <a href="/members/payment_history" class="button">View History</a>
        </div>
    </div>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/hofin/styles/css/admin/dashboard.css">

    <?php require ($_SERVER['DOCUMENT_ROOT'] . '/hofin/navbar.php'); ?>
</head>

<body style="padding-top: 100px;">
    <h1 class="center">DASHBOARD</h1>
    <div class="container">
        <div class="card">
            <h2>Account to be verified</h2>
            <p>Total users: </p>
            <a href="#" class="button red"><i class="fa-regular fa-bell"></i> Verify Account</a>
        </div>
        <div class="card">
            <h2>Total Approved Members</h2>
            <p>Total users:</p>
            <a href="#" class="button"><i class="fa-regular fa-eye"></i> View Approved Accounts</a>
        </div>

        <div class="card">
            <h2>Deleted Accounts</h2>
            <p>Total users: </p>
            <a href="#" class="button"><i class="fa-regular fa-eye"></i> View Deleted Accounts</a>
        </div>
    </div>

    <h1 class="center" style="padding-top: 30px;">Payment Info</h1>
    <div class="container">
        <div class="card">
            <h2>For Payment Approval</h2>
            <p>Unverified Payment:</p>
            <a href="#" class="button red"><i class="fa-regular fa-bell"></i> Verify
                Payment</a>
        </div>
        <div class="card">
            <h2>Approved Payment</h2>
            <p>Verified Payment:</p>
            <a href="#" class="button"><i class="fa-regular fa-eye"></i> View Payments</a>
        </div>
        <div class="card">
            <h2>Members Not Yet paid</h2>
            <p>Total members:</p>
        </div>
        <div class="card">
            <h2>Total Money Collected</h2>
            <p>Total money: <span id="formattedAmount"></span></p>
        </div>

    </div>

</body>
<script>
    function addCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    var amountElement = document.getElementById("formattedAmount");
    amountElement.innerText = addCommas(amountElement.innerText);
</script>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/hofin/styles/css/admin/edit_info.css">
    <?php require ($_SERVER['DOCUMENT_ROOT'] . '/hofin/navbar.php'); ?>

    <title>Edit User Info</title>
</head>

<body>
    <form method="POST" action="">
        <main>
            <h1>EDIT USER INFO</h1>
            <h3>Personal Information</h3>
            <div class="property-information">
                <label for="given_name">Given Name
                    <input type="text" id="given_name" name="given_name" required autocapitalize>
                </label>
                <label for="middle_name">Middle Name
                    <input type="text" id="middle_name" name="middle_name" required>
                </label>

                <label for="last_name">Last Name
                    <input type="text" id="last_name" name="last_name" required>
                </label>
            </div>

            <div class="personal-information up">
                <label for="gender">Gender
                    <select class="select" name="gender" id="gender" required>
                        <!-- {% if info[5] %}
                        {% if info[5] == "Male" %}
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                        {% elif info[5] == "Female" %}
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                        <option value="Other">Other</option>
                        {% elif info[5] == "Other" %}
                        <option value="Other">Other</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        {% else %}
                        {% if info[5] == "" %}
                        <option value="" selected>Please Select One</option>
                        {% else %}
                        <option value="">Please Select One</option>
                        {% endif %}
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                        {% endif %}
                        {% else %} -->
                        <option value="" selected hidden>Please Select One</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                        <!-- {% endif %} -->
                    </select>
                </label>
                <label for="birthdate">Birthday
                    <input type="date" id="bday" name="bday" class="bday" required>
                </label>
            </div>
            <h3>Property Information</h3>
            <div class="property-information">
                <label for="id_no">ID No.
                    <input type="number" id="id_no" name="id_no" required>
                </label>
                <label for="blk_no">Block No.
                    <input type="number" id="blk_no" name="blk_no" required>
                </label>
                <label for="lot_no">Lot No.
                    <input type="number" id="lot_no" name="lot_no" required>
                </label>
                <label for="homelot_area">Homelot Area
                    <input type="number" id="homelot_area" name="homelot_area" required>
                </label>
                <label for="open_space">Open Space
                    <input type="number" id="open_space" name="open_space" required>
                </label>
                <label for="sharein_loan">Share In Loan
                    <input type="text" id="sharein_loan" name="sharein_loan" required
                        oninput="formatNumber(this); calculateTotal()">
                </label>

                <label for="principal_interest">Principal Interest
                    <input type="text" id="principal_interest" name="principal_interest" required
                        oninput="formatNumber(this); calculateTotal()">
                </label>

                <label for="MRI">MRI
                    <input type="text" id="MRI" name="MRI" required oninput="formatNumber(this); calculateTotal()">
                </label>


                <label for="total">Total
                    <input type="text" id="total" name="total" required readonly>
                </label>
            </div>
            <script>
                function formatNumber(input) {
                    // Remove non-numeric characters
                    const value = input.value.replace(/\D/g, '');
                    // Add commas for thousands separator
                    const formattedValue = Number(value).toLocaleString();
                    input.value = formattedValue;
                }

                function calculateTotal() {
                    const principal = parseFloat(document.getElementById('principal_interest').value.replace(/\D/g, ''));
                    const MRI = parseFloat(document.getElementById('MRI').value.replace(/\D/g, ''));
                    const total = principal + MRI;
                    if (!isNaN(total)) {
                        document.getElementById('total').value = total.toLocaleString();
                    } else {
                        document.getElementById('total').value = '';
                    }
                }
            </script>
            <div class="buttons">
                <input class="update button" type="submit" value="&#xf044;  Update"
                    onclick="return confirm('Are you sure you want to update this info?')">
                <a href="/admin/members_info" class="close button"><i class="fa-regular fa-circle-xmark"></i> CLOSE</a>
            </div>
        </main>
    </form>
</body>

</html>
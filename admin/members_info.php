<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Information</title>

    <link rel="stylesheet" href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/hofin/styles/css/admin/members_info.css">
    <?php
    require ($_SERVER['DOCUMENT_ROOT'] . '/hofin/navbar.php');
    $data = getinfo();
    ?>

</head>

<body>
    <h1>USER INFO</h1>
    <div class="half">
        <div class="half-half">
            <h2>Unverified Users:</h2>
        </div>
        <div class="half-half">
            <input type="text" id="searchUnverified" class="search-input"
                onkeyup="filterTable('searchUnverified', 'unverifiedTable')" placeholder="Search for names...">
        </div>
    </div>

    <div class="scrollable">
        <table id="unverifiedTable" class="table">
            <tr>
                <th class="sort-btn" onclick="sortTable(0, 'unverifiedTable')">Last Name</th>
                <th class="sort-btn" onclick="sortTable(1, 'unverifiedTable')">First Name</th>
                <th class="sort-btn" onclick="sortTable(2, 'unverifiedTable')">Middle Name</th>
                <th>Action</th>
                <!-- Add more column headers as per your table structure -->
            </tr>
            <?php if (count($data['unverified_members']) != 0) { ?>
                <?php foreach ($data['unverified_members'] as $unverified) { ?>
                    <tr>
                        <td><?= htmlspecialchars($unverified['last_name']) ?></td>
                        <td><?= htmlspecialchars($unverified['given_name']) ?></td>
                        <td><?= htmlspecialchars($unverified['middle_name']) ?></td>
                        <td>
                            <!-- <form method="POST" action="<?= htmlspecialchars($unverified['user_id']) ?>">
                                <input type="submit" class="edit button" value="&#x2714; APPROVE"
                                    formaction="<?= htmlspecialchars($unverified['user_id']) ?>"
                                    onclick="return confirm('Are you sure you want to APPROVE <?= htmlspecialchars($unverified['given_name']) . ' ' . htmlspecialchars($unverified['last_name']) ?>?')">
                                <input type="submit" class="delete button" value="&#x2716; DECLINE"
                                    formaction="<?= htmlspecialchars($unverified['user_id']) ?>"
                                    onclick="return confirm('Are you sure you want to DECLINE {{unverified[9]}} {{unverified[11]}}?')">
                            </form> -->

                            <!-- <button class="edit button" onclick="openModal(<?= $unverified['user_id'] ?>)">&#x2714;
                                View</button> -->

                            <!-- Button to trigger modal -->
                            <button type="button" class="edit button" data-toggle="modal"
                                data-target="#view-<?= $unverified['user_id'] ?>">
                                <i class="fa fa-eye" aria-hidden="true"></i> View
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="view-<?= $unverified['user_id'] ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <br>
                                            <div class="containeraaa">
                                                <div class="rowaa">
                                                    <?php
                                                    $pic = $unverified['face_pic'];
                                                    if ((isset($pic)) && $pic != false) {
                                                        ?>
                                                        <img draggable="false" src="/hofin/<?php echo $pic; ?>/0.jpg"
                                                            alt="Profile pic" class="img-fluid" />
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <img draggable="false" src="/hofin/face/noprofile.jpg" alt="Profile pic"
                                                            class="img-fluid" />
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="rowaa">
                                                    <h5 class="intro-title">Intro Title</h5>
                                                    <h3>TITLE</h3>
                                                    <p>Some other content, some other content</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Bootstrap JS and dependencies -->
                            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
                            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>

        <!-- <div id="modal-<?= $unverified['user_id'] ?>" class="modal">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal(<?= $unverified['user_id'] ?>)">&times;</span>
                                    <h2>User Information</h2>
                                    <p>User ID: <?= $unverified['user_id'] ?></p>
                                    <p>Email: <?= $unverified['user_id'] ?></p>
                                    <p>Last Name: <?= $unverified['user_id'] ?></p>
                                    <p>First Name: <?= $unverified['user_id'] ?></p>
                                    <p>Middle Name: <?= $unverified['user_id'] ?></p>
                                    <p>Address: <?= $unverified['user_id'] ?></p>
                                    <p>Contact Number: <?= $unverified['user_id'] ?></p>
                                    <p>Birthdate: <?= $unverified['user_id'] ?></p>
                                    <p>Gender: <?= $unverified['user_id'] ?></p>
                                    <p>User Type: <?= $unverified['user_id'] ?></p>
                                </div>
                            </div> -->

        </td>
        <td hidden>Search not found.</td>
        </tr>
    <?php }
            } else { ?>
    <tr>
        <td colspan="6">No unverified users found.</td>
    </tr>
<?php } ?>
</table>
</div>



<div class="half">
    <div class="half-half">
        <h2>Incomplete User Information:</h2>
    </div>
    <div class="half-half">
        <input type="text" id="searchIncomplete" class="search-input"
            onkeyup="filterTable('searchIncomplete', 'incompleteTable')" placeholder="Search for names...">
    </div>
</div>
<div class="scrollable">
    <table id="incompleteTable" class="table">
        <tr>
            <th class="sort-btn" onclick="sortTable(0, 'incompleteTable')">Last Name</th>
            <th class="sort-btn" onclick="sortTable(1, 'incompleteTable')">First Name</th>
            <th class="sort-btn" onclick="sortTable(2, 'incompleteTable')">Middle Name</th>
            <th>Action</th>
            <!-- Add more column headers as per your table structure -->
        </tr>
        <?php if (count($data['incomplete_members']) != 0) { ?>
            <?php foreach ($data['incomplete_members'] as $incomplete) { ?>
                <tr>
                    <td><?= htmlspecialchars($incomplete['last_name']) ?></td>
                    <td><?= htmlspecialchars($incomplete['given_name']) ?></td>
                    <td><?= htmlspecialchars($incomplete['middle_name']) ?></td>
                    <td>
                        <form method="POST" action="<?= htmlspecialchars($unverified['user_id']) ?>">
                            <input type="submit" class="edit button" value="&#xf044; EDIT">
                            <input type="submit" class="delete button" value="&#xf2ed; DELETE"
                                formaction="/admin/delete_info/{{incs[1]}}"
                                onclick="return confirm('Are you sure you want to delete this account?')">
                        </form>
                    </td>
                    <td hidden>Search not found.</td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="6">No unverified users found.</td>
            </tr>
        <?php } ?>
    </table>
</div>


<div class="half">
    <div class="half-half">
        <h2>Complete User Information:</h2>
    </div>
    <div class="half-half">
        <input type="text" id="searchComplete" class="search-input"
            onkeyup="filterTable('searchComplete', 'completeTable')" placeholder="Search for names...">
    </div>
</div>

<div class="scrollable">
    <table id="completeTable" class="table">
        <tr>
            <th class="sort-btn" onclick="sortTable(0, 'completeTable')">Last Name</th>
            <th class="sort-btn" onclick="sortTable(1, 'completeTable')">First Name</th>
            <th class="sort-btn" onclick="sortTable(2, 'completeTable')">Middle Name</th>
            <th>Action</th>
        </tr>
        <?php if (count($data['completed_members']) != 0) { ?>
            <?php foreach ($data['completed_members'] as $complete) { ?>
                <tr>
                    <td><?= htmlspecialchars($complete['last_name']) ?></td>
                    <td><?= htmlspecialchars($complete['given_name']) ?></td>
                    <td><?= htmlspecialchars($complete['middle_name']) ?></td>
                    <td>
                        <form method="POST" action="<?= htmlspecialchars($unverified['user_id']) ?>">
                            <input type="submit" class="edit button" value="&#xf044; EDIT">
                            <input type="submit" class="delete button" value="&#xf2ed; DELETE"
                                formaction="/admin/delete_info/{{incs[1]}}"
                                onclick="return confirm('Are you sure you want to delete this account?')">
                        </form>
                    </td>
                    <td hidden>Search not found.</td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="6">No unverified users found.</td>
            </tr>
        <?php } ?>
</div>

</body>

<script>
    function filterTable(inputId, tableId) {
        var input, filter, table, tr, td, i, j, txtValue, rowVisible, noSearchTd;
        input = document.getElementById(inputId);
        filter = input.value.toUpperCase();
        table = document.getElementById(tableId);
        tr = table.getElementsByTagName("tr");
        rowVisible = false;

        for (i = 1; i < tr.length; i++) { // Start from index 1 to skip the header row
            td = tr[i].getElementsByTagName("td");
            tr[i].style.display = "none";
            noSearchTd = tr[i].getElementsByClassName("no-search")[0];

            if (noSearchTd) {
                noSearchTd.hidden = true;
            }

            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        rowVisible = true;
                        break;
                    }
                }
            }
        }

        // Show the 'Search not found' message if no row is visible
        if (!rowVisible) {
            var newRow = table.insertRow(-1);
            var newCell = newRow.insertCell(0);
            newCell.colSpan = tr[0].cells.length;
            newCell.className = "no-search";
            newCell.textContent = "Search not found.";
        }
    }

    function sortTable(columnIndex, tableId) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById(tableId);
        switching = true;
        dir = "asc"; // Set the sorting direction to ascending by default

        while (switching) {
            switching = false;
            rows = table.rows;

            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("td")[columnIndex];
                y = rows[i + 1].getElementsByTagName("td")[columnIndex];

                if (dir == "asc") {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }
</script>


</html>
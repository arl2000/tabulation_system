<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Criteria</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2, h3 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }

        .centered-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            table-layout: fixed; /* Ensures proportional cells */
        }

        th, td {
            padding: 12px;
            text-align: center; /* Center content horizontally */
            border: 1px solid #ddd;
            width: 33%; /* Adjust as needed for the number of columns */
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .addbtn, .deletebtn {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .addbtn:hover, .deletebtn:hover {
            background-color: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            position: relative; /* Added for close button positioning */
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        label {
            display: block;
            margin-bottom: 8px;
            text-align: center;
        }

        input[type="text"], input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .add-sub-criteria, .delete-criteria, .delete-sub-criteria {
            color: #4caf50;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .add-sub-criteria:hover, .delete-criteria:hover, .delete-sub-criteria:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php
    include 'db_connection.php';

    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit;
    }

    if (!isset($_GET['event_id'])) {
        echo "Event ID is not set.";
        exit;
    }

    $event_id = $_GET['event_id'];

    // Fetch event name based on event ID
    $event_query = "SELECT name FROM events WHERE event_id = '$event_id'";
    $event_result = mysqli_query($connect, $event_query);
    if ($event_result && mysqli_num_rows($event_result) > 0) {
        $event = mysqli_fetch_assoc($event_result);
        $event_name = $event['name'];
    } else {
        echo "Event not found.";
        exit;
    }

    // Fetch all criteria
    $criteria_query = "SELECT * FROM criteria WHERE event_id = '$event_id'";
    $criteria_result = mysqli_query($connect, $criteria_query);
    ?>

    <div class="centered-container">
        <h3>Category and Criteria for: <?php echo htmlspecialchars($event_name); ?></h3>

        <?php while ($criteria = mysqli_fetch_assoc($criteria_result)): ?>
            <table>
                <thead>
                    <tr>
                        <th colspan="3">
                            <a href="#" class="add-sub-criteria" data-criteria-id="<?php echo $criteria['criteria_id']; ?>"><?php echo htmlspecialchars($criteria['name']); ?></a>
                            <a href="#" class="delete-criteria" data-criteria-id="<?php echo $criteria['criteria_id']; ?>">Delete</a>
                        </th>
                    </tr>
                    <tr>
                        <th>Criteria</th>
                        <th>Points</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $criteria_id = $criteria['criteria_id'];
                    $sub_criteria_query = "SELECT * FROM sub_criteria WHERE criteria_id = '$criteria_id'";
                    $sub_criteria_result = mysqli_query($connect, $sub_criteria_query);
                    while ($sub_criteria = mysqli_fetch_assoc($sub_criteria_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sub_criteria['sub_criteria_name']); ?></td>
                            <td><?php echo htmlspecialchars($sub_criteria['points']); ?></td>
                            <td>
                                <a href="#" class="delete-sub-criteria" data-sub-criteria-id="<?php echo $sub_criteria['sub_criteria_id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($sub_criteria_result) == 0): ?>
                        <tr>
                            <td colspan="3">No sub-criteria</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endwhile; ?>

        <!-- Button to add a new criterion -->
        <button class="addbtn" style="display: none;" id="showModal-<?php echo $event_id; ?>"></button>
    </div>

    <!-- The Modal for Adding Sub-Criteria -->
    <div id="subCriteriaModal-<?php echo $event_id; ?>" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Sub-Criteria</h2>
            <form id="subCriteriaForm-<?php echo $event_id; ?>">
                <input type="hidden" id="criteria_id-<?php echo $event_id; ?>" name="criteria_id">
                <label for="sub_criteria_name-<?php echo $event_id; ?>">Sub-Criteria Name:</label>
                <input type="text" id="sub_criteria_name-<?php echo $event_id; ?>" name="sub_criteria_name" required><br>
                <label for="points-<?php echo $event_id; ?>">Points:</label>
                <input type="number" id="points-<?php echo $event_id; ?>" step="0.01" name="points" required><br>
                <input type="submit" value="Add Sub-Criteria">
                <button type="button" onclick="document.getElementById('subCriteriaModal-<?php echo $event_id; ?>').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <!-- The Modal for Adding Criterion -->
    <div id="categoryModal-<?php echo $event_id; ?>" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Criterion for Event: <?php echo htmlspecialchars($event_name); ?></h2>
            <form method="post">
                <label for="name-<?php echo $event_id; ?>">Category Name:</label>
                <input type="text" id="name-<?php echo $event_id; ?>" name="name" required><br>
                <input type="submit" value="Add Criterion">
            </form>
        </div>
    </div>

    <script>
    // Sub-Criteria modal handling
    document.querySelectorAll('.add-sub-criteria').forEach(button => {
        button.addEventListener('click', function() {
            const criteriaId = this.getAttribute('data-criteria-id');
            document.getElementById('criteria_id-<?php echo $event_id; ?>').value = criteriaId;
            document.getElementById('subCriteriaModal-<?php echo $event_id; ?>').style.display = 'block';
        });
    });

    document.getElementById('subCriteriaForm-<?php echo $event_id; ?>').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('add_sub_criteria.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            window.location.reload();
        });
    });

    // Get modals and close elements
    var subCriteriaModal = document.getElementById('subCriteriaModal-<?php echo $event_id; ?>');
    var categoryModal = document.getElementById('categoryModal-<?php echo $event_id; ?>');

    var subCriteriaClose = subCriteriaModal.querySelector('.close');
    var categoryClose = categoryModal.querySelector('.close');

    // Close modals when close button is clicked
    subCriteriaClose.onclick = function() {
        subCriteriaModal.style.display = 'none';
    }

    categoryClose.onclick = function() {
        categoryModal.style.display = 'none';
    }

    // Close modals when clicking anywhere outside the modal content
    window.onclick = function(event) {
        if (event.target == subCriteriaModal) {
            subCriteriaModal.style.display = 'none';
        }
        if (event.target == categoryModal) {
            categoryModal.style.display = 'none';
        }
    }

    // Open category modal
    document.getElementById("showModal-<?php echo $event_id; ?>").onclick = function() {
        categoryModal.style.display = "block";
    }

    // Delete criteria functionality
    document.querySelectorAll('.delete-criteria').forEach(button => {
        button.addEventListener('click', function() {
            const criteriaId = this.getAttribute('data-criteria-id');
            if (confirm('Are you sure you want to delete this criterion?')) {
                fetch(`delete_category.php?criteria_id=${criteriaId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    window.location.reload();
                });
            }
        });
    });

    // Delete sub-criteria functionality
    document.querySelectorAll('.delete-sub-criteria').forEach(button => {
        button.addEventListener('click', function() {
            const subCriteriaId = this.getAttribute('data-sub-criteria-id');
            if (confirm('Are you sure you want to delete this sub-criterion?')) {
                fetch(`delete_criteria.php?sub_criteria_id=${subCriteriaId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    window.location.reload();
                });
            }
        });
    });
    </script>
</body>
</html>

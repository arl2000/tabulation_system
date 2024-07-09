<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    $query = "INSERT INTO events (name) VALUES ('$name')";
    if (mysqli_query($connect, $query)) {
        echo "<script>";
        echo "alert('New event created successfully');";
        echo "window.location.href = 'admin_dashboard.php';";
        echo "</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <style>
        /* Existing styles for the login form and dashboard */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 50px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2, h3 {
    color: #333;
    margin-bottom: 20px;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    display: inline;
    margin-right: 10px;
}

ul li a {
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
    transition: color 0.3s;
}

ul li a:hover {
    color: #0056b3;
}

.event-list h4 {
    margin: 15px 0;
}

.event-list h4 a {
    text-decoration: none;
    color: #333;
    transition: color 0.3s;
}

.event-list h4 a:hover {
    color: #007bff;
}

/* Styles for the event creation form */
form {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
}

form input[type="text"] {
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

form input[type="submit"] {
    padding: 10px;
    background-color: #28a745;
    border: none;
    border-radius: 4px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

form input[type="submit"]:hover {
    background-color: #218838;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Create New Event</h2>
        <form method="post">
            <label for="name">Event Name:</label>
            <input type="text" id="name" name="name" required>
            <input type="submit" value="Create Event">
        </form>
    </div>
</body>
</html>

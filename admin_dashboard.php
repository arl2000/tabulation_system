<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch the admin username
$admins_query = "SELECT username FROM admins";
$admins_result = mysqli_query($connect, $admins_query);
$admin = mysqli_fetch_assoc($admins_result);

// Fetch all events
$events_query = "SELECT * FROM events";
$events_result = mysqli_query($connect, $events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }

        header h2 {
            color: #333;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
        }

        nav ul li {
            margin-right: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            padding: 10px 15px;
            border: 2px solid transparent;
            border-radius: 4px;
            transition: all 0.3s;
        }

        nav ul li a:hover {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        h3 {
            color: #333;
            margin: 20px 0;
        }

        .event-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .event-list table, .event-list th, .event-list td {
            border: 1px solid #ddd;
        }

        .event-list th, .event-list td {
            padding: 12px;
            text-align: left;
        }

        .event-list th {
            background-color: #007bff;
            color: white;
        }

        .event-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .event-list tr:hover {
            background-color: #f1f1f1;
        }

        .event-list a {
            text-decoration: none;
            color: #007bff;
            transition: color 0.3s;
        }

        .event-list a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Welcome Admin: <?php echo $admin['username']; ?></h2>
            <nav>
                <ul>
                    <li><a href="create_event.php">Create Event</a></li>
                </ul>
            </nav>
        </header>

        <h3>Events Summary</h3>
        <div class="event-list">
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                        <tr>
                            <td><?php echo $event['name']; ?></td>
                            <td>
                                <a href="manage_events.php?event_id=<?php echo $event['event_id']; ?>">Manage Event</a>
                                <!-- Add other actions if needed -->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

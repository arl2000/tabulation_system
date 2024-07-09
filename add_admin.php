<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
    if (mysqli_query($connect, $query)) {
        echo "New admin added successfully";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connect);
    }
}
?>

<form method="post">
    Username: <input type="text" name="username">
    Password: <input type="password" name="password">
    <input type="submit" value="Add Admin">
</form>

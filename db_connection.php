<?php
    $dbUser = 'root'; 
    $dbHost = 'localhost'; 
    $dbPassword = ''; 
    $dbName = 'tabulation'; 

    try {
        $connect = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);
    } catch (\Throwable $th) {
        echo $th;
    }
?>

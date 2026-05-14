<?php
    $dns = 'mysql:host = localhost; dbname = travel_guide_db';
    $username = 'root';


    try {
        $db = new PDO($dns, $username);
    } catch (PDOException $e) {
        $error = "Database Error: ";
        $error .= $e->getMessage();
        include('view/error.php');
        exit();
    }


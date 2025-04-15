<?php 

// Define the parameters required for the database connection
$host = 'mysql'; // The MySQL service name in Docker
$dbUser   = 'v.je'; // The MySQL username specified in the docker-compose file
$dbPass = 'v.je'; // The password for the MySQL user defined in the docker-compose file
$dbName = "assignment1"; // The name of the database used in this application

try { 
    // Create a new PDO instance to establish a database connection
    $dbConnection = new PDO('mysql:host=' . $host . ';dbname=' . $dbName, $dbUser  , $dbPass); 
    // Set PDO to throw exceptions for errors, enhancing error management
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    // Configure the character encoding to utf8mb4 to accommodate a broader range of characters
    $dbConnection->exec("SET NAMES utf8mb4"); 
} catch (PDOException $e) { 
    // Handle any connection errors gracefully
    die("Database connection error: " . htmlspecialchars($e->getMessage())); 
} 
?>
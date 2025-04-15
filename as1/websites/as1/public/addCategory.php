<?php
session_start(); // Begin the session to access session variables
require 'connectionpage.php'; // Include the file for database connection

// Verify if the user is logged in and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php'); // Redirect to the login page if the user is not logged in or not an admin
    exit; // Terminate further execution of the script
}

// If the admin submits the form, we proceed to add the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; // Retrieve the category name from the form submission

    // Insert the new category into the database
    $stmt = $dbConnection->prepare('INSERT INTO categories (name) VALUES (?)'); // Prepare the SQL insert statement
    $stmt->execute([$name]); // Execute the insert statement with the provided category name

    // After adding the category, redirect back to the category management page
    header('Location: adminCategories.php'); // Redirect to the category management page
    exit; // Stop further execution of the script
}
?>

<?php include 'header.php'; // Include the header section ?>

<h2>Add Category</h2>

<!-- Form for the admin to add a new category -->
<form method="POST"> <!-- Start the form with the POST method -->
    <label>Name: <input type="text" name="name" required></label> <!-- Input field for the category name -->
    <input type="submit" value="Add"> <!-- Button to submit the form -->
</form>

<?php include 'footer.php'; // Include the footer section ?>
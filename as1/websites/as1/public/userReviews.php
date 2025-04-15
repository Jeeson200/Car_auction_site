<?php
session_start(); // Start the session to manage user authentication
require 'connectionpage.php'; // Include the database connection script to interact with the database

// Verify if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php'); // Redirect to the login page if the user is not authenticated
    exit; // Stop further execution
}

// Check if a user ID is provided in the URL
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id']; // Retrieve the user ID from the URL

    // Prepare and execute the SQL statement to fetch reviews for the specified user
    $stmt = $dbConnection->prepare('SELECT * FROM reviews WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]); // Execute the query with the user ID as a parameter
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all reviews as an associative array
} else {
    exit('No user ID provided.'); // Exit if no user ID is specified in the URL
}

include 'header.php'; // Include the header section of the page
?>

<h2>User Reviews</h2>

<?php if ($reviews): // Check if there are any reviews to display ?>
    <ul>
        <?php foreach ($reviews as $review): // Loop through each review ?>
            <li>
                <h3><?= htmlspecialchars($review['review_text']) ?></h3> <!-- Display the review text safely -->
                <p><em>Posted on: <?= date('F j, Y', strtotime($review['created_at'])) ?></em></p> <!-- Format and display the review date -->
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: // If no reviews are found for the user ?>
    <p>No reviews found for this user.</p> <!-- Message indicating no reviews are available -->
<?php endif; ?>

<?php include 'footer.php'; // Include the footer section of the page ?>
<?php
session_start(); // Start the session to manage user authentication
require 'connectionpage.php'; // Include the database connection script for database interactions

// Initialize variables for search results and search term
$searchResults = []; // Array to hold the search results
$searchTerm = ''; // Variable to store the search term

// Process the search form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']); // Remove any leading or trailing whitespace from the search term
    
    // Prepare the SQL statement to search for auctions based on title or description, including the highest bid
    $stmt = $dbConnection->prepare('
        SELECT auctions.*, users.name AS seller, 
        COALESCE(MAX(bids.bid_amount), 0) AS current_bid 
        FROM auctions 
        JOIN users ON auctions.user_id = users.id 
        LEFT JOIN bids ON auctions.id = bids.auction_id 
        WHERE title LIKE ? OR description LIKE ? 
        GROUP BY auctions.id 
        ORDER BY 
            CASE 
                WHEN title LIKE ? THEN 1 
                WHEN description LIKE ? THEN 2 
                ELSE 3 
            END, title ASC
    ');
    
    // Use wildcards for searching to match any part of the title or description
    $likeTerm = '%' . $searchTerm . '%'; // Create a search pattern with wildcards
    $stmt->execute([$likeTerm, $likeTerm, $likeTerm, $likeTerm]); // Execute the query with the search parameters
    
    // Fetch all matching results as an associative array
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php'; // Include the header section of the page
?>

<main>
    <h2>Search Results</h2>

    <?php if ($searchResults): // Check if there are any search results ?>
        <h3>Results for "<?= htmlspecialchars($searchTerm) ?>"</h3> <!-- Display the search term -->
        <ul class="carList"> <!-- List of search results -->
            <?php foreach ($searchResults as $car): // Loop through each car in the results ?>
                <li>
                    <?php if (!empty($car['image_path'])): // Check if the car has an associated image ?>
                        <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="Image of <?= htmlspecialchars($car['title']) ?>" style="width: 100px; height: auto;">
                    <?php else: // If no image is available ?>
                        <img src="default-car.png" alt="Default image" style="width: 100px; height: auto;"> <!-- Use a default image -->
                    <?php endif; ?>
                    <article>
                        <h2><a href="auction.php?id=<?= htmlspecialchars($car['id']) ?>"><?= htmlspecialchars($car['title']) ?></a></h2> <!-- Display the car title -->
                        <h3>Seller: <?= htmlspecialchars($car['seller']) ?></h3> <!-- Display the seller's name -->
                        <p><?= nl2br(htmlspecialchars($car['description'])) ?></p> <!-- Display the car description -->
                        <p class="price">Current bid: £<?= number_format($car['current_bid'], 2) ?></p> <!-- Display the current bid amount -->
                        <a class="more auctionLink" href="auction.php?id=<?= htmlspecialchars($car['id']) ?>">More &gt;&gt;</a> <!-- Link to the auction details -->
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: // If no results are found ?>
        <p>No results found for "<?= htmlspecialchars($searchTerm) ?>".</p> <!-- Message indicating no results -->
    <?php endif; ?>
</main>

<?php include 'footer.php'; // Include the footer section of the page ?>

<!-- References:
1. SQL query structure and logic are based on standard practices for searching and filtering data in relational databases, as discussed in CSY2028 lecture notes.
2. The use of COALESCE and aggregate functions is a common SQL technique for handling optional data and ensuring default values.
3. The overall structure follows best practices for PHP and SQL integration, ensuring security and efficiency in data retrieval.
-->
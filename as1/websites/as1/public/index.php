<?php 

session_start(); // Initiate the session to manage user authentication 
require 'connectionpage.php'; // Include the script that establishes the database connection 

// Determine if the user is currently logged in 
$isLoggedIn = isset($_SESSION['user']); 

// Retrieve the 10 most recent auctions from the database, including the highest bid for each 
$recentAuctions = $dbConnection->query(' 
SELECT auctions.*, users.name AS seller, categories.name AS category,  
COALESCE(MAX(bids.bid_amount), 0) AS current_bid  
FROM auctions  
LEFT JOIN users ON auctions.user_id = users.id  
LEFT JOIN categories ON auctions.category_id = categories.id  
LEFT JOIN bids ON auctions.id = bids.auction_id  
GROUP BY auctions.id  
ORDER BY auctions.end_date DESC  
LIMIT 10 
')->fetchAll(PDO::FETCH_ASSOC); // Fetch the results as an associative array 
// Reference: SQL query structure adapted from CSY2028 lecture notes 

?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Carbuy Auctions</title> 
    <link rel="stylesheet" href="carbuy.css" /> <!-- Link to the external CSS stylesheet for styling --> 
</head> 

<body> 
<header> 
    <h1> 
        <span class="C">C</span> 
        <span class="a">a</span> 
        <span class="r">r</span> 
        <span class="b">b</span> 
        <span class="u">u</span> 
        <span class="y">y</span> 
    </h1> 

    <!-- Search form allowing users to find cars by entering search terms --> 
    <form action="search.php" method="GET"> 
        <input type="text" name="search" placeholder="Search for a car" required /> 
        <input type="submit" name="submit" value="Search" /> 
    </form> 
</header> 

<nav> 
    <ul> 
        <!-- Navigation links for different car categories --> 
        <li><a class="categoryLink" href="category.php?category=estate">Estate</a></li> 
        <li><a class="categoryLink" href="category.php?category=electric">Electric</a></li> 
        <li><a class="categoryLink" href="category.php?category=coupe">Coupe</a></li> 
        <li><a class="categoryLink" href="category.php?category=saloon">Saloon</a></li> 
        <li><a class="categoryLink" href="category.php?category=4x4">4x4</a></li> 
        <li><a class="categoryLink" href="category.php?category=sports">Sports</a></li> 
        <li><a class="categoryLink" href="category.php?category=hybrid">Hybrid</a></li> 
        <li><a class="categoryLink" href="category.php?category=more">More</a></li> 
    </ul> 
</nav> 

<nav> 
    <ul> 
        <!-- Navigation links for user actions based on their login status --> 
        <?php if ($isLoggedIn): ?> 
            <li><a href="addAuction.php">Add Auction</a></li> 
            <li><a href="logout.php">Logout</a></li> 
        <?php else: ?> 
            <li><a href="login.php">Login</a></li> 
        <?php endif; ?> 
    </ul> 
</nav> 

<img src="banners/1.jpg" alt="Banner" /> <!-- Display a banner image for visual appeal --> 

<main> 
<h1>Latest Car Listings</h1> 
<ul class="carList"> 
    <?php foreach ($recentAuctions as $auction): ?> 
    <li> 
        <img src="<?= htmlspecialchars($auction['image_path'] ?? 'default-car.png') ?>" alt="<?= htmlspecialchars($auction['title']) ?>"> <!-- Use a default image if none is available --> 
        <article> 
            <h2><a href="auction.php?id=<?= htmlspecialchars($auction['id']) ?>"><?= htmlspecialchars($auction['title']) ?></a></h2> 
            <h3><?= htmlspecialchars($auction['category']) ?></h3> 
            <p><?= htmlspecialchars($auction['description']) ?></p> 
            <p class="price">Current bid: £<?= number_format($auction['current_bid'], 2) ?></p> 
            <a href="auction.php?id=<?= htmlspecialchars($auction['id']) ?>" class="more auctionLink">More &gt;&gt;</a> 
        </article> 
    </li> 
    <?php endforeach; ?> 
</ul> 

<hr /> 

<footer> 
    &copy; Carbuy 2024 
</footer> 
</main> 
</body> 
</html> 
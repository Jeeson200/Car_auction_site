<?php 

session_start(); // Start the session 
require 'connectionpage.php'; // Include the database connection file 

// Get the auction ID from the URL (e.g., auction.php?id=2) 
$auctionId = $_GET['id'] ?? null; 

// If no auction ID is provided, show an error message 
if (!$auctionId) { 
    echo "No auction selected. Please provide a valid auction ID in the URL."; 
    exit; 
} 

// Fetch auction details along with seller and category information 
$stmt = $dbConnection->prepare(' 
SELECT auctions.*, users.name AS seller, categories.name AS category  
FROM auctions  
JOIN users ON auctions.user_id = users.id  
JOIN categories ON auctions.category_id = categories.id  
WHERE auctions.id = ? 
'); 
$stmt->execute([$auctionId]); 
$auction = $stmt->fetch(); 

// If no auction is found, show an error message 
if (!$auction) { 
    echo "This auction doesn’t exist."; 
    exit; 
} 

// Fetch the highest bid for the auction 
$highestBid = $dbConnection->prepare('SELECT MAX(bid_amount) FROM bids WHERE auction_id = ?'); 
$highestBid->execute([$auctionId]); 
$highest = $highestBid->fetchColumn(); 

// Handle bid submission 
if (isset($_POST['bid']) && isset($_SESSION['user'])) { 
    $bidAmount = $_POST['bid']; 
    $userId = $_SESSION['user']['id']; 

    // Check if the bid amount is greater than the current highest bid 
    if ($highest === null || $bidAmount > $highest) { 
        // Save the bid into the bids table 
        $placeBid = $dbConnection->prepare('INSERT INTO bids (bid_amount, auction_id, user_id) VALUES (?, ?, ?)'); 
        $placeBid->execute([$bidAmount, $auctionId, $userId]); 

        // Set a success message 
        $_SESSION['success_message'] = "Your bid has been placed successfully."; 
        // Reload the page to show the new bid 
        header("Location: auction.php?id=$auctionId"); 
        exit; 
    } else { 
        $errorMessage = "Your bid must be higher than the current highest bid of £" . number_format($highest, 2); 
    } 
} 

// Handle review submission for the auction's author 
if (isset($_POST['reviewText']) && isset($_SESSION['user'])) { 
    $text = $_POST['reviewText']; 
    // Save the review into the reviews table 
    $addReview = $dbConnection->prepare('INSERT INTO reviews (review_text, user_id, auction_id) VALUES (?, ?, ?)'); 
    $addReview->execute([$text, $_SESSION['user']['id'], $auctionId]); // Use auctionId here 

    // Set a success message 
    $_SESSION['success_message'] = "Your review has been added successfully."; 
    // Refresh to show the new review 
    header("Location: auction.php?id=$auctionId"); 
    exit; 
} 

// Fetch all reviews for the auction 
$reviews = $dbConnection->prepare(' 
SELECT reviews.*, users.name AS reviewer  
FROM reviews  
JOIN users ON reviews.user_id = users.id  
WHERE auction_id = ?  
ORDER BY created_at DESC 
'); 
$reviews->execute([$auctionId]); // Use auctionId to fetch reviews for the specific auction 
$allReviews = $reviews->fetchAll(); 

// Fetch bid history for the auction 
$bidHistory = $dbConnection->prepare('SELECT * FROM bids WHERE auction_id = ? ORDER BY created_at DESC'); 
$bidHistory->execute([$auctionId]); 
$bids = $bidHistory->fetchAll(); 

// Determine if the auction has ended 
$currentDateTime = new DateTime(); 
$endDateTime = new DateTime($auction['end_date']); 
$auctionEnded = $currentDateTime > $endDateTime; // Flag to indicate if the auction has ended 
?> 

<?php include 'header.php'; ?> <!-- Include the header file --> 

<!-- Showing the auction --> 
<article class="car"> 
    <img src="car.png" alt="Car image"> <!-- Placeholder for car image --> 

    <section class="details"> 
        <h2><?= htmlspecialchars($auction['title']) ?></h2> 
        <h3>Category: <?= htmlspecialchars($auction['category']) ?></h3> 
        <p>Auction created by <a href="#"><?= htmlspecialchars($auction['seller']) ?></a></p> 

        <!-- Showing the current highest bid --> 
        <p class="price">Current bid: £<?= $highest ? number_format($highest, 2) : '0.00' ?></p> 

        <time>Ends: <?= date('d M Y, H:i ', strtotime($auction['end_date'])) ?></time> 

        <!-- Check if the auction has ended --> 
        <?php if ($auctionEnded): ?> 
            <p>This auction has ended. No more bids can be placed.</p> 
        <?php else: ?> 
            <!-- Bidding form for logged-in users --> 
            <?php if (isset($_SESSION['user'])): ?> 
                <form method="POST" class="bid"> 
                    <input type="number" name="bid" required min="0" step="0.01" placeholder="Enter bid amount" /> 
                    <input type="submit" value="Place bid" /> 
                </form> 
                <?php if (isset($errorMessage)): ?> 
                    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p> 
                <?php endif; ?> 
                <?php if (isset($_SESSION['success_message'])): ?> 
                    <p style="color: green;"><?= htmlspecialchars($_SESSION['success_message']) ?></p> 
                    <?php unset($_SESSION['success_message']); ?> 
                <?php endif; ?> 
            <?php else: ?> 
                <p><em>Login to place a bid.</em></p> 
            <?php endif; ?> 
        <?php endif; ?> 
    </section> 

    <section class="description"> 
        <!-- Description of the auction --> 
        <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p> 
    </section> 

    <section class="reviews"> 
        <h2>Reviews for <?= htmlspecialchars($auction['seller']) ?></h2> 

        <!-- Listing all reviews left for the seller --> 
        <ul> <?php if (count($allReviews) > 0): ?> 
            <?php foreach ($allReviews as $review): ?> 
                <li> 
                    <strong><?= htmlspecialchars($review['reviewer']) ?> said:</strong> 
                    <?= htmlspecialchars($review['review_text']) ?> 
                    <em><?= date('d/m/Y', strtotime($review['created_at'])) ?></em> 
                </li> 
            <?php endforeach; ?> 
        <?php else: ?> 
            <li>No reviews yet. Be the first to leave a review!</li> 
        <?php endif; ?> 
        </ul> 

        <!-- Review form if user is logged in --> 
        <?php if (isset($_SESSION['user'])): ?> 
            <form method="POST"> 
                <label>Add your review</label> 
                <textarea name="reviewText" required></textarea> 
                <input type="submit" value="Add Review" /> 
            </form> 
            <?php if (isset($_SESSION['success_message'])): ?> 
                <p style="color: green;"><?= htmlspecialchars($_SESSION['success_message']) ?></p> 
                <?php unset($_SESSION['success_message']); ?> 
            <?php endif; ?> 
        <?php else: ?> 
            <p><em>Login to leave a review.</em></p> 
        <?php endif; ?> 
    </section> 

    <!-- Bid History Section --> 
    <section class="bid-history"> 
        <h3>Bid History</h3> 
        <ul> 
            <?php foreach ($bids as $bid): ?> 
                <li>£<?= htmlspecialchars($bid['bid_amount']) ?> by User ID: <?= htmlspecialchars($bid['user_id']) ?> on <?= date('d/m/Y H:i', strtotime($bid['created_at'])) ?></li> 
            <?php endforeach; ?> 
        </ul> 
    </section> 

    <!-- Return to Auction Button --> 
    <div class="return-to-auction"> 
        <a href="auction_list.php" class="button">Return to Auction List</a> <!-- Button to return to auction list --> 
    </div> 
</article> 

<?php include 'footer.php'; ?> <!-- Include the footer file --> 
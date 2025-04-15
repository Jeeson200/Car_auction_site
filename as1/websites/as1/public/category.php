<?php 

session_start(); 
require 'connectionpage.php'; 

// Get the category ID from the URL and validate it 
$categoryId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null; 

// If no valid category ID is provided, redirect to the homepage 
if (!$categoryId) { 
    header('Location: index.php'); 
    exit; 
} 

// Fetch category information 
$stmt = $dbConnection->prepare('SELECT * FROM categories WHERE id = ?'); 
$stmt->execute([$categoryId]); 
$category = $stmt ->fetch(PDO::FETCH_ASSOC); 

// If the category does not exist, redirect to the homepage 
if (!$category) { 
    header('Location: index.php'); 
    exit; 
} 

// Fetch auctions for the selected category 
$stmt = $dbConnection->prepare(' 
SELECT auctions.*, users.name AS seller  
FROM auctions  
JOIN users ON auctions.user_id = users.id  
WHERE auctions.category_id = ?  
ORDER BY end_date ASC 
'); 
$stmt->execute([$categoryId]); 
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC); 

include 'header.php'; 
?> 

<main> 
<h1>Auctions in Category: <?= htmlspecialchars($category['name']) ?></h1> 

<?php if ($auctions): ?> 
<ul class="carList"> 
    <?php foreach ($auctions as $auction): ?> 
    <li> 
        <img src="<?= htmlspecialchars($auction['image_path'] ?? 'car.png') ?>" alt="Car image for <?= htmlspecialchars($auction['title']) ?>"> <!-- Use car.png as default --> 
        <article> 
            <h2><?= htmlspecialchars($auction['title']) ?></h2> 
            <h3>Seller: <?= htmlspecialchars($auction['seller']) ?></h3> 
            <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p> 
            <p class="price"> 
                Current bid: £<?= number_format($auction['currentBid'] ?? 0, 2) ?> 
            </p> 
            <a class="more auctionLink" href="auction.php?id=<?= htmlspecialchars($auction['id']) ?>">More &gt;&gt;</a> 
        </article> 
    </li> 
    <?php endforeach; ?> 
</ul> 
<?php else: ?> 
<p>No auctions found in this category.</p> 
<?php endif; ?> 
</main> 

<?php include 'footer.php'; ?> 
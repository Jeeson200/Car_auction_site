<?php
session_start();
require 'connectionpage.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the auction ID from the URL
$auctionId = $_GET['id'] ?? null;

// If there's no ID, we stop right here
if (!$auctionId) {
    echo "No auction selected. Please provide a valid auction ID in the URL.";
    exit;
} else {
    echo "Auction ID: " . htmlspecialchars($auctionId); // Debugging line
}

// Fetch auction details
$stmt = $datapageConnection->prepare('SELECT * FROM auction WHERE id = ? AND user_id = ?');
$stmt->execute([$auctionId, $_SESSION['user']['id']]);
$auction = $stmt->fetch();

// If no auction is found, show error
if (!$auction) {
    echo "This auction doesn’t exist or you do not have permission to edit it.";
    exit;
}

// Handle form submission for updating auction
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = $_POST['category'];
    $endDate = $_POST['endDate'];

    // Handle image upload
    $imagePath = $auction['imagePath']; // Keep the existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $error = "Error: Only JPG, PNG, and GIF files are allowed.";
        } elseif ($_FILES['image']['size'] > 2000000) { // Limit to 2MB
            $error = "Error: File size must be less than 2MB.";
        } else {
            // Create a unique file name
            $imageName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName); // Sanitize filename
            $imagePath = "public/images/auctions/" . $imageName;

            // Check if the directory exists and create it if not
            if (!is_dir('public/images/auctions')) {
                mkdir('public/images/auctions', 0755, true);
            }

            // Move the uploaded file to the desired directory
            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                $error = "Error uploading the image.";
 }
        }
    }

    // If no errors, update the auction in the database
    if (empty($error)) {
        $updateStmt = $datapageConnection->prepare('UPDATE auction SET title = ?, description = ?, categoryId = ?, endDate = ?, imagePath = ? WHERE id = ?');
        $updateStmt->execute([$title, $description, $categoryId, $endDate, $imagePath, $auctionId]);

        // Redirect to the auction page after editing
        header("Location: auction.php?id=$auctionId");
        exit();
    }
}

// Handle auction deletion
if (isset($_POST['delete'])) {
    $deleteStmt = $datapageConnection->prepare('DELETE FROM auction WHERE id = ? AND user_id = ?');
    $deleteStmt->execute([$auctionId, $_SESSION['user']['id']]);

    // Redirect to the homepage after deletion
    header("Location: index.php");
    exit();
}

// Fetch categories for the select box
$categories = $datapageConnection->query('SELECT * FROM category')->fetchAll();

include 'header.php'; // Include header
?>

<main>
    <h1>Edit Auction</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        Title: <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required>
        Description: <textarea name="description" required><?= htmlspecialchars($auction['description']) ?></textarea>
        Category: 
        <select name="category" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $category['id'] == $auction['categoryId'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        End Date: <input type="datetime-local" name="endDate" value="<?= date('Y-m-d\TH:i', strtotime($auction['endDate'])) ?>" required>
        Image: <input type="file" name="image" accept="image/*" />
        <input type="submit" value="Update Auction">
    </form>

    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this auction?');">
        <input type="submit" name="delete" value="Delete Auction" style="color: red;">
    </form>
</main>

<?php include 'footer.php'; // Include footer ?>
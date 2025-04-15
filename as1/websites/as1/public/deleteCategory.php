<?php 

session_start(); 
require 'connectionpage.php'; 

// Admin check again, just to be safe 
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] !== 1) { 
    header('Location: login.php'); 
    exit; 
} 

// Get the category ID from the link 
$id = $_GET['id'] ?? null; 

// If we have an ID, delete the category from the table 
if ($id) { 
    // Check if the category exists 
    $checkStmt = $dbConnection->prepare('SELECT * FROM categories WHERE id = ?'); 
    $checkStmt->execute([$id]); 
    $category = $checkStmt->fetch(); 

    if ($category) { 
        // Delete all auctions associated with this category 
        $deleteAuctionsStmt = $dbConnection->prepare('DELETE FROM auctions WHERE category_id = ?'); 
        $deleteAuctionsStmt->execute([$id]); 

        // Now delete the category 
        $stmt = $dbConnection->prepare('DELETE FROM categories WHERE id = ?'); 
        $stmt->execute([$id]); 

        // Set a success message 
        $_SESSION['message'] = "Category and associated auctions deleted successfully."; 
    } else { 
        // Set an error message if the category does not exist 
        $_SESSION['error'] = "Category not found."; 
    } 
} else { 
    // Set an error message if no ID is provided 
    $_SESSION['error'] = "No category ID provided."; 
} 

// Redirect back to category manager page 
header('Location: adminCategories.php'); 
exit; 
?>
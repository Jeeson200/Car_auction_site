<?php 

session_start(); // Start the session to access session variables 
require 'connectionpage.php'; // Include the database connection file 

// Ensure only admin users can see this page 
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] !== 1) { 
    header('Location: login.php'); // Redirect to login if not logged in or not an admin 
    exit; // Stop further execution 
} 

// Fetch all categories from the database 
try { 
    $categories = $dbConnection->query('SELECT * FROM categories')->fetchAll(); // Fetch all categories 
} catch (PDOException $e) { 
    echo "Error fetching categories: " . htmlspecialchars($e->getMessage()); // Display error message if fetching fails 
    exit; // Stop further execution 
} 
?> 

<?php include 'header.php'; // Include header ?> 

<h2>Manage Categories</h2> 

<!-- Link to go to add category page --> 
<p><a href="addCategory.php" class="button">Add New Category</a></p> 

<!-- Listing all categories with edit and delete options --> 
<ul> 
<?php if (count($categories) > 0): ?> <!-- Check if there are categories --> 
    <?php foreach ($categories as $cat): ?> <!-- Loop through each category --> 
        <li> 
            <?= htmlspecialchars($cat['name']) ?> <!-- Display category name safely --> 
            - <a href="editCategory.php?id=<?= htmlspecialchars($cat['id']) ?>">Edit</a> <!-- Link to edit category --> 
            - <a href="deleteCategory.php?id=<?= htmlspecialchars($cat['id']) ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a> <!-- Link to delete category with confirmation --> 
        </li> 
    <?php endforeach; ?> 
<?php else: ?> 
    <li>No categories found.</li> <!-- Message if no categories exist --> 
<?php endif; ?> 
</ul> 

<?php include 'footer.php'; // Include footer ?> 
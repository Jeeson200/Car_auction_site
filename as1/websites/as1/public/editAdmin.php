<?php 
session_start(); // Start the session
require 'connectionpage.php'; // Include the database connection file 

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) { 
    header('Location: login.php'); 
    exit; 
} 

// Fetch the admin details for editing
if (isset($_GET['id'])) { 
    $id = (int)$_GET['id']; // Cast to integer for security
    $stmt = $dbConnection->prepare('SELECT * FROM users WHERE id = ? AND isAdmin = 1'); 
    $stmt->execute([$id]); 
    $admin = $stmt->fetch(PDO::FETCH_ASSOC); 

    if (!$admin) { 
        die("Admin not found."); // Handle case where admin does not exist 
    } 
} else { 
    die("Invalid request."); // Handle case where no ID is provided 
} 

// Handle form submission for updating admin details
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL); 
    $name = htmlspecialchars(trim($_POST['name'])); 

    // Prepare SQL statement to update admin details
    $stmt = $dbConnection->prepare('UPDATE users SET email = ?, name = ? WHERE id = ?'); 
    if ($stmt->execute([$email, $name, $id])) { 
        header('Location: manageAdmins.php'); // Redirect after successful update 
        exit; 
    } else { 
        $error = "Failed to update admin details. Please try again."; // Set error message if update fails 
    } 
} 

include 'header.php'; 
?> 

<h2>Edit Admin</h2> 

<!-- Display error message if there was an error --> 
<?php if (isset($error)): ?> 
    <p style="color: red;"><?= htmlspecialchars($error) ?></p> 
<?php endif; ?> 

<!-- Form for editing admin details --> 
<form method="POST"> 
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></label><br> 
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required></label><br> 
    <input type="submit" value="Update Admin"> 
</form> 

<?php include 'footer.php'; ?> 
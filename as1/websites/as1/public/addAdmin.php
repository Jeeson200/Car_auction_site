<?php  
session_start(); // Start the session to use session variables  
require 'connectionpage.php'; // Include the database connection script  
// Check if the user is logged in and has admin privileges  
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] !== 1) {  
header('Location: login.php'); // Redirect to login if not an admin  
exit; // Stop further execution  
}  
// Handle the form submission for adding a new admin  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL); // Sanitize email input  
$password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Securely hash the password  
$name = htmlspecialchars(trim($_POST['name'])); // Sanitize name input to prevent XSS  
// Check if the email is already registered  
$checkEmailStmt = $dbConnection->prepare('SELECT COUNT(*) FROM users WHERE email = ?');  
$checkEmailStmt->execute([$email]);  
$emailExists = $checkEmailStmt->fetchColumn(); // Get the count of existing emails  
if ($emailExists) {  
$error = "This email is already in use."; // Set error message if email exists  
} else {  
// Insert the new admin's details into the database  
$stmt = $dbConnection->prepare('INSERT INTO users (email, password, name, isAdmin) VALUES (?, ?, ?, 1)');  
if ($stmt->execute([$email, $password, $name])) {  
header('Location: manageAdmins.php'); // Redirect to manage admins page after successful addition  
exit; // Stop further execution  
} else {  
$error = "Unable to add the admin. Please try again."; // Set error message if insertion fails  
}  
}  
}  
include 'header.php'; // Include the header section of the page  
?>  
<h2>Add Admin</h2>  
<!-- Display error message if the email is already registered or if there was an error -->  
<?php if (isset($error)): ?>  
<p style="color: red;"><?= htmlspecialchars($error) ?></p> <!-- Show error message in red -->  
<?php endif; ?>  
<!-- Form for adding a new admin -->  
<form method="POST">  
<label>Email: <input type="email" name="email" required></label><br>  
<label>Password: <input type="password" name="password" required></label><br>  
<label>Name: <input type="text" name="name" required></label><br>  
<input type="submit" value="Add Admin">  
</form>  
<?php include 'footer.php'; // Include the footer section of the page ?> 
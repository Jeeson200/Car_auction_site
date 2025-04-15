<?php 
session_start();
require 'connectionpage.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;

    // Check if email already exists
    $checkEmail = $dbConnection->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $checkEmail->execute([$email]);
    $emailExists = $checkEmail->fetchColumn();

    if ($emailExists > 0) {
        echo "<p>This email is already registered. Please use a different email.</p>";
    } else {
        // Insert new user
        $addUser  = $dbConnection->prepare('INSERT INTO users (email, password, name, isAdmin) VALUES (?, ?, ?, ?)');
        if ($addUser ->execute([$email, $password, $name, $isAdmin])) {
            header('Location: login.php');
            exit;
        } else {
            echo "<p>Failed to register. Please try again.</p>";
        }
    }
}
?>

<?php include 'header.php'; ?>
<h2>Register</h2>
<form method="POST">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label><input type="checkbox" name="isAdmin"> Register as Admin</label><br>
    <input type="submit" value="Register">
</form>
<?php include 'footer.php'; ?>
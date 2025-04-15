<?php
session_start(); // Start the session

// Check if there is a logout message
$logoutMessage = 'you have sucessfullly logged out';
if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message']; // Store the message
    unset($_SESSION['logout_message']); // Clear the message after displaying it
}

// Include the database connection
require 'connectionpage.php';  // Ensure the path to the connection file is correct

// Initialize error variable
$error = '';

// Only run this part if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Getting the data submitted through the form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);  // Store the password in the variable

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        // Checking if this email exists in the users table
        $checkUser  = $dbConnection->prepare('SELECT * FROM users WHERE email = ?');
        $checkUser ->execute([$email]);

        // Get the user details if found
        $user = $checkUser ->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

        // If the user is found and the password matches, log them in
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Saving the user data in the session to keep them logged in
            $_SESSION['user'] = $user;

            // Check if the user is an admin
            if ($user['isAdmin']) {
                // Redirect to the admin dashboard
                header('Location: admin_dashboard.php');  // Change to your admin page
            } else {
                // Redirect to the user homepage
                header('Location: index.php');  // Change to your user homepage
            }
            exit;
        } else {
            // If email/password doesn't match, show an error
            $error = 'Email or password is incorrect.';
        }
    }
}
?>

<?php include 'header.php'; ?>
<h2>Log In</h2>

<!-- If login fails, this will show the error message -->
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<!-- Display logout message if it exists -->
<?php if (!empty($logoutMessage)) echo "<p style='color:green;'>$logoutMessage</p>"; ?>

<!-- Login form -->
<form method="POST">
    <label>Email:
        <input type="email" name="email" required>
    </label><br>
    <label>Password:
        <input type="password" name="password" required>
    </label><br>
    <input type="submit" name="submit" value="Login">
</form>

<?php include 'footer.php'; ?>
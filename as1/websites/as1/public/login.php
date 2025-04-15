<?php  

session_start(); 
require 'connectionpage.php'; // Include the database connection 

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 

    // Validate email format 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = 'Invalid email format.'; 
    } else { 
        // Check if the user exists 
        $checkUser = $dbConnection->prepare('SELECT * FROM users WHERE email = ?'); 
        $checkUser->execute([$email]); 
        $user = $checkUser->fetch(PDO::FETCH_ASSOC); 

        // Verify password 
        if ($user && password_verify($password, $user['password'])) { 
            session_regenerate_id(true); // Prevent session fixation 
            $_SESSION['user'] = $user; // Store user data in session 

            // Redirect based on user role 
            if ($user['isAdmin']) { 
                header('Location: adminDashboard.php'); 
            } else { 
                header('Location: index.php'); 
            } 
            exit; 
        } else { 
            $error = 'Email or password is incorrect.'; 
        } 
    } 
} 
?> 

<?php include 'header.php'; ?> 
<h2>Log In</h2> 
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?> 
<form method="POST"> 
    <label>Email: <input type="email" name="email" required></label><br> 
    <label>Password: <input type="password" name="password" required></label><br> 
    <input type="submit" value="Login"> 
</form> 
<?php include 'footer.php'; ?> 
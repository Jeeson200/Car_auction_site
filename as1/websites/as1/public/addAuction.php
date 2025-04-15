<?php 

session_start(); // Start the session to access session variables 
require 'connectionpage.php'; // Include the database connection file 

// Check if the user is logged in 
if (!isset($_SESSION['user'])) { 
    header("Location: login.php"); // Redirect to login if not logged in 
    exit(); // Stop further execution 
} 

$error = null; // Variable to store any error messages 

// Process the form submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $title = trim($_POST['title']); // Get and trim the title input 
    $description = trim($_POST['description']); // Get and trim the description input 
    $category_id = $_POST['category']; // Get the selected category ID 
    $end_date = $_POST['endDate']; // Get the end date input 
    $user_id = $_SESSION['user']['id']; // Get the ID of the logged-in user 

    // Handle image upload 
    $imagePath = null; // Initialize the variable for the image path 
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { // Check if an image file was uploaded 
        $imageTmpPath = $_FILES['image']['tmp_name']; // Temporary path of the uploaded file 
        $imageName = basename($_FILES['image']['name']); // Get the original name of the uploaded image 

        // Validate the file type 
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Define allowed image types 
        if (!in_array($_FILES['image']['type'], $allowedTypes)) { 
            $error = "Error: Only JPG, PNG, and GIF files are allowed."; // Set error message for unsupported file type 
        } elseif ($_FILES['image']['size'] > 2000000) { // Check if the file size exceeds 2MB 
            $error = "Error: File size must be less than 2MB."; // Set error message for file size limit 
        } else { 
            // Generate a unique file name 
            $imageName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName); // Sanitize the filename 
            $imagePath = "public/images/auctions/" . $imageName; // Define the path for the uploaded image 

            // Check if the directory exists and create it if it doesn't 
            if (!is_dir('public/images/auctions')) { 
                mkdir('public/images/auctions', 0755, true); // Create the directory with appropriate permissions 
            } 

            // Move the uploaded file to the specified directory 
            if (!move_uploaded_file($imageTmpPath, $imagePath)) { 
                $error = "Error uploading the image."; // Set error message if the upload fails 
            } 
        } 
    } else { 
        $error = "Error: No image uploaded or there was an upload error."; // Set error message if no image was uploaded 
    } 

    // If there are no errors, check if the user exists and insert the new auction into the database 
    if (empty($error)) { 
        // Check if the user exists
        $userCheckStmt = $dbConnection->prepare('SELECT COUNT(*) FROM users WHERE id = ?');
        $userCheckStmt->execute([$user_id]);
        $userExists = $userCheckStmt->fetchColumn();

        if (!$userExists) {
            $error = "Error: User does not exist."; // Set error message if user is not found
        } else {
            // Proceed with the insert if no errors
            $stmt = $dbConnection->prepare('INSERT INTO auctions (title, description, category_id, end_date, user_id, image_path) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $description, $category_id, $end_date, $user_id, $imagePath]);
            
            // Redirect to the auction page after successfully adding the auction 
            header("Location: auction.php?id=" . $dbConnection->lastInsertId()); // Redirect to the newly created auction 
            exit(); // Stop further execution 
        }
    } 
} 

// Retrieve categories for the dropdown selection 
$categories = $dbConnection->query('SELECT * FROM categories')->fetchAll(); // Fetch all categories from the database 

include 'header.php'; // Include the header section 
?> 

<main> 
<h1>Add Auction</h1> 
<?php if (isset($error)): ?> 
    < p style="color:red;"><?= htmlspecialchars($error) ?></p> <!-- Safely display any error messages --> 
<?php endif; ?> 
<form method="POST" enctype="multipart/form-data"> <!-- Start the form with POST method and file upload capability --> 
    Title: <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required> <!-- Input for auction title --> 
    Description: <textarea name="description" required><?= htmlspecialchars($description ?? '') ?></textarea> <!-- Input for auction description --> 
    Category:  
    <select name="category" required> <!-- Dropdown menu for categories --> 
        <?php foreach ($categories as $category): ?> 
            <option value="<?= $category['id'] ?>" <?= (isset($category_id) && $category_id == $category['id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option> <!-- Display category options --> 
        <?php endforeach; ?> 
    </select> 
    End Date: <input type="datetime-local" name="endDate" value="<?= htmlspecialchars($end_date ?? '') ?>" required> <!-- Input for auction end date --> 
    Image: <input type="file" name="image" accept="image/*" required /> <!-- Input for image upload --> 
    <input type="submit" value="Add Auction"> <!-- Button to submit the form --> 
</form> 
</main> 

<?php include 'footer.php'; // Include the footer section ?> 
<?php
session_start();
require 'connectionpage.php';

// Only admins allowed in here
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php');
    exit;
}

// Getting the category ID from the link
$id = $_GET['id'] ?? null;

// If no ID, we stop the page
if (!$id) {
    echo "No category selected.";
    exit;
}

// Pulling the category info from DB so we can show current value
$stmt = $dbConnection->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$category = $stmt->fetch();

// Check if the category exists
if (!$category) {
    echo "Category not found.";
    exit;
}

// If the form is submitted, we update the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name']);

    // Validate the new category name
    if (empty($newName)) {
        $errorMessage = "Category name cannot be empty.";
    } elseif (strlen($newName) < 3) {
        $errorMessage = "Category name must be at least 3 characters long.";
    } else {
        // Update the category in the database
        $update = $datapageConnection->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $update->execute([$newName, $id]);

        // Redirect to the categories management page
        header('Location: adminCategories.php');
        exit;
    }
}
?>

<?php include 'header.php'; ?>

<h2>Edit Category</h2>

<!-- Show error message if exists -->
<?php if (isset($errorMessage)): ?>
    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

<!-- Show the current category value in the input box -->
<form method="POST">
    <label>Name:
        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
    </label>
    <input type="submit" value="Update">
</form>

<?php include 'footer.php'; ?>
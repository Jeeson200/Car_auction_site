<?php
session_start();
require 'connectionpage.php'; // Ensure this file contains the correct database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php');
    exit;
}

// Handle deletion of an admin
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the admin from the database
    $stmt = $datapageConnection->prepare('DELETE FROM users WHERE id = ? AND isAdmin = 1');
    $stmt->execute([$id]);

    // Redirect back to manage admins page
    header('Location: manageAdmins.php');
    exit;
}

// Fetch all admin users
$stmt = $dbConnection->query('SELECT * FROM users WHERE isAdmin = 1');
$admins = $stmt->fetchAll();

include 'header.php';
?>

<h2>Manage Admins</h2>

<!-- List of admin users -->
<ul>
    <?php foreach ($admins as $admin): ?>
        <li>
            <?= htmlspecialchars($admin['name']) ?> (<?= htmlspecialchars($admin['email']) ?>)
            <a href="editAdmin.php?id=<?= $admin['id'] ?>">Edit</a>
            <a href="manageAdmins.php?action=delete&id=<?= $admin['id'] ?>" onclick="return confirm('Are you sure you want to delete this admin?');">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Link to add a new admin -->
<a href="addAdmin.php">Add New Admin</a>

<?php include 'footer.php'; ?>
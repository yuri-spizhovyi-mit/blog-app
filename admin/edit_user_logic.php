<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Admin-only protection
if (!isset($_SESSION['user-id']) || !isset($_SESSION['user_is_admin'])) {
    header('Location: ' . ROOT_URL . 'signin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate ID
    $id = $_POST['id'] ?? '';
    if (!ctype_digit($id)) {
        $_SESSION['edit-user-error'] = "Invalid user ID.";
        header('Location: ' . ROOT_URL . 'admin/manage_users.php');
        exit;
    }
    $id = (int)$id;

    // Sanitize inputs
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $is_admin  = filter_var($_POST['userrole'] ?? 0, FILTER_VALIDATE_INT);

    // Validation
    if ($firstname === '' || $lastname === '') {
        $_SESSION['edit-user-error'] = "First name and last name cannot be empty.";
        header('Location: ' . ROOT_URL . 'admin/manage_users.php');
        exit;
    }

    // Prepare safe update query
    $stmt = $connection->prepare(
        "UPDATE users SET firstname = ?, lastname = ?, is_admin = ? WHERE id = ? LIMIT 1"
    );

    $stmt->bind_param("ssii", $firstname, $lastname, $is_admin, $id);

    if ($stmt->execute()) {
        $_SESSION['edit-user-success'] = "User {$firstname} {$lastname} updated successfully!";
    } else {
        $_SESSION['edit-user-error'] = "Failed to update user.";
    }

    header('Location: ' . ROOT_URL . 'admin/manage_users.php');
    exit;
}

// Direct access fallback
header('Location: ' . ROOT_URL . 'admin/manage_users.php');
exit;
?>

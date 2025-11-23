<?php
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username_email = trim($_POST['username_email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($username_email === '') {
        $_SESSION['signin-error'] = "Username or Email is required.";
    } elseif ($password === '') {
        $_SESSION['signin-error'] = "Password is required.";
    } else {
        // Prepare safe query
        $stmt = $connection->prepare(
            "SELECT id, username, email, password, is_admin FROM users WHERE username = ? OR email = ? LIMIT 1"
        );
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // User found
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                $_SESSION['user-id'] = $user['id'];

                if ((int)$user['is_admin'] === 1) {
                    $_SESSION['user_is_admin'] = true;
                }

                header("Location: " . ROOT_URL . "admin/index.php");
                exit;

            } else {
                $_SESSION['signin-error'] = "Incorrect credentials. Please try again.";
            }

        } else {
            $_SESSION['signin-error'] = "User not found.";
        }
    }

    // Save old form input
    $_SESSION['signin-data'] = $_POST;

    header("Location: " . ROOT_URL . "signin.php");
    exit;
}

// If accessed directly
header("Location: " . ROOT_URL . "signin.php");
exit;
?>

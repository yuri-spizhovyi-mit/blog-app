<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Ensure only admins can access this logic
if (!isset($_SESSION['user-id']) || !isset($_SESSION['user_is_admin'])) {
    header('Location: ' . ROOT_URL . 'signin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. SANITIZE INPUTS
    $firstname       = trim($_POST['firstname'] ?? '');
    $lastname        = trim($_POST['lastname'] ?? '');
    $username        = trim($_POST['username'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $createpassword  = $_POST['createpassword'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    $is_admin        = filter_var($_POST['userrole'] ?? 0, FILTER_VALIDATE_INT);
    $avatar          = $_FILES['avatar'] ?? null;

    // 2. VALIDATE INPUTS
    if ($firstname === '') {
        $_SESSION['add-user-error'] = "Please enter a first name.";
    } elseif ($lastname === '') {
        $_SESSION['add-user-error'] = "Please enter a last name.";
    } elseif ($username === '') {
        $_SESSION['add-user-error'] = "Please enter a username.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['add-user-error'] = "Please enter a valid email address.";
    } elseif (strlen($createpassword) < 6) {
        $_SESSION['add-user-error'] = "Password must be at least 6 characters.";
    } elseif ($createpassword !== $confirmpassword) {
        $_SESSION['add-user-error'] = "Passwords do not match.";
    } elseif (!$avatar || !$avatar['name']) {
        $_SESSION['add-user-error'] = "Please upload a profile picture.";
    }

    // If validation failed, redirect with form data
    if (isset($_SESSION['add-user-error'])) {
        $_SESSION['add-user-data'] = [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'username'  => $username,
            'email'     => $email
        ];

        header('Location: ' . ROOT_URL . 'admin/add_user.php');
        exit;
    }

    // 3. CHECK IF USERNAME OR EMAIL ALREADY EXISTS
    $stmt = $connection->prepare(
        "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1"
    );
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $existing = $stmt->get_result();

    if ($existing->num_rows > 0) {
        $_SESSION['add-user-error'] = "Username or email already exists.";
        $_SESSION['add-user-data'] = $_POST;

        header('Location: ' . ROOT_URL . 'admin/add_user.php');
        exit;
    }

    // 4. PROCESS AVATAR FILE
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['add-user-error'] = "Avatar must be a JPG, JPEG, or PNG file.";
        $_SESSION['add-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'admin/add_user.php');
        exit;
    }

    if ($avatar['size'] > 2 * 1024 * 1024) { // 2 MB max
        $_SESSION['add-user-error'] = "Avatar file must be less than 2MB.";
        $_SESSION['add-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'admin/add_user.php');
        exit;
    }

    // Generate secure filename
    $avatar_filename = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    $upload_path = __DIR__ . '/../images/' . $avatar_filename;

    if (!move_uploaded_file($avatar['tmp_name'], $upload_path)) {
        $_SESSION['add-user-error'] = "Failed to upload avatar.";
        $_SESSION['add-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'admin/add_user.php');
        exit;
    }

    // 5. HASH PASSWORD
    $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

    // 6. INSERT USER INTO DATABASE
    $stmt = $connection->prepare(
        "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssssssi",
        $firstname,
        $lastname,
        $username,
        $email,
        $hashed_password,
        $avatar_filename,
        $is_admin
    );

    if ($stmt->execute()) {
        $_SESSION['add-user-success'] = "New user {$firstname} {$lastname} added successfully!";
        header('Location: ' . ROOT_URL . 'admin/manage_users.php');
        exit;
    } else {
        $_SESSION['add-user-error'] = "Failed to add new user.";
        $_SESSION['add-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'admin/add_user.php');
        exit;
    }

} else {
    header('Location: ' . ROOT_URL . 'admin/add_user.php');
    exit;
}
?>

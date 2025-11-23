<?php
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================================
       1. SANITIZE INPUTS
    ================================= */
    $firstname       = trim($_POST['firstname'] ?? '');
    $lastname        = trim($_POST['lastname'] ?? '');
    $username        = trim($_POST['username'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $createpassword  = $_POST['createpassword'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    $avatar          = $_FILES['avatar'] ?? null;

    /* ================================
       2. VALIDATION
    ================================= */
    if ($firstname === '') {
        $_SESSION['signup-error'] = "Please enter your first name.";
    } elseif ($lastname === '') {
        $_SESSION['signup-error'] = "Please enter your last name.";
    } elseif ($username === '') {
        $_SESSION['signup-error'] = "Please choose a username.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['signup-error'] = "Please enter a valid email address.";
    } elseif (strlen($createpassword) < 6) {
        $_SESSION['signup-error'] = "Password must be at least 6 characters.";
    } elseif ($createpassword !== $confirmpassword) {
        $_SESSION['signup-error'] = "Passwords do not match.";
    } elseif (!$avatar || !$avatar['name']) {
        $_SESSION['signup-error'] = "Please upload a profile image.";
    }

    /* If any validation failed → redirect */
    if (isset($_SESSION['signup-error'])) {
        $_SESSION['signup-data'] = [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'username'  => $username,
            'email'     => $email
        ];

        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    /* ================================
       3. CHECK UNIQUE USERNAME/EMAIL
    ================================= */
    $stmt = $connection->prepare(
        "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1"
    );
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $exists = $stmt->get_result();

    if ($exists->num_rows > 0) {
        $_SESSION['signup-error'] = "Username or email already exists.";
        $_SESSION['signup-data'] = $_POST;

        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    /* ================================
       4. VALIDATE & SAVE AVATAR
    ================================= */
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['signup-error'] = "Avatar must be a JPG, JPEG, or PNG file.";
        $_SESSION['signup-data'] = $_POST;

        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    if ($avatar['size'] > 2 * 1024 * 1024) { // 2MB limit
        $_SESSION['signup-error'] = "Avatar file must be less than 2MB.";
        $_SESSION['signup-data'] = $_POST;

        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    // Unique file name
    $new_filename = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    $upload_path = __DIR__ . "/images/" . $new_filename;

    if (!move_uploaded_file($avatar['tmp_name'], $upload_path)) {
        $_SESSION['signup-error'] = "Failed to upload avatar. Try again.";
        $_SESSION['signup-data'] = $_POST;

        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    /* ================================
       5. HASH PASSWORD
    ================================= */
    $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

    /* ================================
       6. INSERT USER INTO DATABASE
    ================================= */
    $stmt = $connection->prepare(
        "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin)
         VALUES (?, ?, ?, ?, ?, ?, 0)"
    );
    $stmt->bind_param(
        "ssssss",
        $firstname,
        $lastname,
        $username,
        $email,
        $hashed_password,
        $new_filename
    );

    if ($stmt->execute()) {
        $_SESSION['signup-success'] = "Registration successful. Please sign in.";
        header("Location: " . ROOT_URL . "signin.php");
        exit;
    } else {
        $_SESSION['signup-error'] = "Registration failed. Try again.";
        $_SESSION['signup-data'] = $_POST;

        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

} else {
    header("Location: " . ROOT_URL . "signup.php");
    exit;
}
?>

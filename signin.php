<?php
require_once __DIR__ . '/config/constants.php';

// Restore old form input if available
$username_email = $_SESSION['signin-data']['username_email'] ?? '';
unset($_SESSION['signin-data']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog App — Sign In</title>

    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<section class="form_section">
    <div class="container form_section-container">
        <h2>Sign In</h2>

        <!-- SUCCESS MESSAGE (from signup_logic.php) -->
        <?php if (isset($_SESSION['signup-success'])) : ?>
            <div class="alert_message success">
                <p><?= htmlspecialchars($_SESSION['signup-success']); ?></p>
            </div>
            <?php unset($_SESSION['signup-success']); ?>
        <?php endif; ?>

        <!-- ERROR MESSAGE (from signin_logic.php) -->
        <?php if (isset($_SESSION['signin-error'])) : ?>
            <div class="alert_message error">
                <p><?= htmlspecialchars($_SESSION['signin-error']); ?></p>
            </div>
            <?php unset($_SESSION['signin-error']); ?>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>signin_logic.php" method="POST">
            <input type="text" 
                   name="username_email" 
                   value="<?= htmlspecialchars($username_email) ?>" 
                   placeholder="Username or Email" 
                   required>

            <input type="password" 
                   name="password" 
                   placeholder="Password" 
                   required>

            <button type="submit" name="submit" class="btn">Sign In</button>

            <small>
                Don't have an account? 
                <a href="signup.php">Sign up</a>
            </small>
        </form>

    </div>
</section>

</body>
</html>

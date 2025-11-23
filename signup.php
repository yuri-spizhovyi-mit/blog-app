<?php
require_once __DIR__ . '/config/constants.php';

// Restore previous form data if signup failed
$firstname = htmlspecialchars($_SESSION['signup-data']['firstname'] ?? '');
$lastname  = htmlspecialchars($_SESSION['signup-data']['lastname'] ?? '');
$username  = htmlspecialchars($_SESSION['signup-data']['username'] ?? '');
$email     = htmlspecialchars($_SESSION['signup-data']['email'] ?? '');

unset($_SESSION['signup-data']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog App — Sign Up</title>

    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<section class="form_section">
    <div class="container form_section-container">
        <h2>Sign Up</h2>

        <!-- ERROR MESSAGE -->
        <?php if (isset($_SESSION['signup-error'])) : ?>
            <div class="alert_message error">
                <p><?= htmlspecialchars($_SESSION['signup-error']); ?></p>
            </div>
            <?php unset($_SESSION['signup-error']); ?>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>signup_logic.php" method="POST" enctype="multipart/form-data">

            <input type="text" 
                   name="firstname" 
                   value="<?= $firstname ?>" 
                   placeholder="First Name" 
                   required>

            <input type="text" 
                   name="lastname" 
                   value="<?= $lastname ?>" 
                   placeholder="Last Name" 
                   required>

            <input type="text" 
                   name="username" 
                   value="<?= $username ?>" 
                   placeholder="Username" 
                   required>

            <input type="email" 
                   name="email" 
                   value="<?= $email ?>" 
                   placeholder="Email" 
                   required>

            <input type="password" 
                   name="createpassword"  
                   placeholder="Create Password" 
                   minlength="6"
                   required>

            <input type="password" 
                   name="confirmpassword"  
                   placeholder="Confirm Password"
                   minlength="6"
                   required>

            <div class="form_control">
                <label for="avatar">User Avatar</label>
                <input type="file" name="avatar" id="avatar" accept=".png,.jpg,.jpeg">
            </div>

            <button type="submit" name="submit" class="btn">Sign Up</button>

            <small>
                Already have an account?
                <a href="<?= ROOT_URL ?>signin.php">Sign In</a>
            </small>
        </form>

    </div>
</section>

</body>
</html>

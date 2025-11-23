<?php
require_once __DIR__ . '/../partials/header.php';

// Restore previous form data after validation errors
$firstname = htmlspecialchars($_SESSION['add-user-data']['firstname'] ?? '');
$lastname  = htmlspecialchars($_SESSION['add-user-data']['lastname'] ?? '');
$username  = htmlspecialchars($_SESSION['add-user-data']['username'] ?? '');
$email     = htmlspecialchars($_SESSION['add-user-data']['email'] ?? '');

unset($_SESSION['add-user-data']);
?>
<section class="form_section">
    <div class="container form_section-container">
        <h2>Add User</h2>

        <?php if (isset($_SESSION['add-user-error'])) : ?>
            <div class="alert_message error">
                <p><?= htmlspecialchars($_SESSION['add-user-error']); ?></p>
            </div>
            <?php unset($_SESSION['add-user-error']); ?>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>admin/add-user-logic.php" method="POST" enctype="multipart/form-data">

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

            <select name="userrole" required>
                <option value="0">Author</option>
                <option value="1">Admin</option>
            </select>

            <div class="form_control">
                <label for="avatar">User Avatar</label>
                <input type="file" 
                       name="avatar" 
                       id="avatar" 
                       accept=".png,.jpg,.jpeg"
                       required>
            </div>

            <button type="submit" name="submit" class="btn">Add User</button>

        </form>
    </div>
</section>
<?php
require_once __DIR__ . '/../partials/footer.php';
?>

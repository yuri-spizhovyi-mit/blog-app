<?php
require_once __DIR__ . '/../partials/header.php';

// Admin-only protection
if (!isset($_SESSION['user-id']) || !isset($_SESSION['user_is_admin'])) {
    header('Location: ' . ROOT_URL . 'signin.php');
    exit;
}

// Validate GET id
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: ' . ROOT_URL . 'admin/manage_users.php');
    exit;
}

$id = (int) $_GET['id'];

// Fetch user safely
$stmt = $connection->prepare("SELECT id, firstname, lastname, is_admin FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: ' . ROOT_URL . 'admin/manage_users.php');
    exit;
}
?>
<section class="form_section">
    <div class="container form_section-container">
        <h2>Edit User</h2>

        <form action="<?= ROOT_URL ?>admin/edit-user-logic.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

            <input type="text"
                   name="firstname"
                   value="<?= htmlspecialchars($user['firstname']) ?>"
                   placeholder="First Name"
                   required>

            <input type="text"
                   name="lastname"
                   value="<?= htmlspecialchars($user['lastname']) ?>"
                   placeholder="Last Name"
                   required>

            <select name="userrole" required>
                <option value="0" <?= $user['is_admin'] ? '' : 'selected' ?>>Author</option>
                <option value="1" <?= $user['is_admin'] ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit" name="submit" class="btn">Update User</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

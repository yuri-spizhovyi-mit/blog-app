<?php
require_once __DIR__ . '/../partials/header.php';

// Ensure admin access
if (!isset($_SESSION['user-id']) || !isset($_SESSION['user_is_admin'])) {
    header('Location: ' . ROOT_URL . 'signin.php');
    exit;
}

$current_admin_id = $_SESSION['user-id'];

// Safe query
$stmt = $connection->prepare("SELECT id, firstname, lastname, username, is_admin FROM users WHERE id != ?");
$stmt->bind_param("i", $current_admin_id);
$stmt->execute();
$users = $stmt->get_result();
?>
<section class="dashboard">

<?php
$alerts = [
    'add-user-success' => 'success',
    'edit-user-success' => 'success',
    'edit-user-error' => 'error',
    'delete-user-error' => 'error',
    'delete-user-success' => 'success'
];

foreach ($alerts as $key => $type) {
    if (isset($_SESSION[$key])) {
        echo '<div class="alert_message ' . $type . ' container"><p>' . 
             htmlspecialchars($_SESSION[$key]) . '</p></div>';
        unset($_SESSION[$key]);
    }
}
?>

    <div class="container dashboard_container">
        <button id="show_sidebar-btn" class="sidebar_toogle">
            <i class="uil uil-angle-right-b"></i></button>
        <button id="hide_sidebar-btn" class="sidebar_toogle">
            <i class="uil uil-angle-left-b"></i></button>

        <aside>
            <ul>
                <li><a href="add_post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
                <li><a href="index.php"><i class="uil uil-create-dashboard"></i><h5>Manage Post</h5></a></li>
                <li><a href="add_user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
                <li><a href="manage_users.php" class="active"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
                <li><a href="add_category.php"><i class="uil uil-folder-plus"></i><h5>Add Category</h5></a></li>
                <li><a href="manage_categories.php"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
            </ul>
        </aside>

        <main>
            <h2>Manage Users</h2>

            <?php if ($users->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>

                        <td>
                          <a href="<?= ROOT_URL ?>admin/edit_user.php?id=<?= $user['id'] ?>" class="btn sm">
                              Edit
                          </a>
                        </td>

                        <td>
                          <a href="<?= ROOT_URL ?>admin/delete_user.php?id=<?= $user['id'] ?>" 
                             class="btn sm danger"
                             onclick="return confirm('Are you sure you want to delete this user?');">
                             Delete
                          </a>
                        </td>

                        <td><?= $user['is_admin'] ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>

            <?php else: ?>
                <div class="alert_message error">No users found.</div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

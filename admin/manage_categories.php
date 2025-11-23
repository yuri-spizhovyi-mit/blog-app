<?php
require_once __DIR__ . '/../partials/header.php';

// Admin-only access
if (!isset($_SESSION['user-id']) || !isset($_SESSION['user_is_admin'])) {
    header('Location: ' . ROOT_URL . 'signin.php');
    exit;
}

// Fetch categories
$query = "SELECT id, title FROM categories ORDER BY title ASC";
$categories = mysqli_query($connection, $query);
?>
<section class="dashboard">

<?php
$alerts = [
    'add-category-success' => 'success',
    'add-category-error' => 'error',
    'edit-category-success' => 'success',
    'edit-category-error' => 'error',
    'delete-category-success' => 'success',
    'delete-category-error' => 'error'
];

foreach ($alerts as $key => $type):
    if (isset($_SESSION[$key])):
?>
<div class="alert_message <?= $type ?> container">
    <p><?= htmlspecialchars($_SESSION[$key]) ?></p>
</div>
<?php unset($_SESSION[$key]); endif; endforeach; ?>

<div class="container dashboard_container">
    <aside>
        <ul>
            <li><a href="add_post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
            <li><a href="index.php"><i class="uil uil-create-dashboard"></i><h5>Manage Posts</h5></a></li>
            <li><a href="add_user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
            <li><a href="manage_users.php"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
            <li><a href="add_category.php"><i class="uil uil-folder-plus"></i><h5>Add Category</h5></a></li>
            <li><a href="manage_categories.php" class="active"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
        </ul>
    </aside>

    <main>
        <h2>Manage Categories</h2>

        <?php if (mysqli_num_rows($categories) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td><?= htmlspecialchars($category['title']) ?></td>
                    <td><a href="<?= ROOT_URL ?>admin/edit_category.php?id=<?= $category['id'] ?>" class="btn sm">Edit</a></td>
                    <td><a href="<?= ROOT_URL ?>admin/delete_category.php?id=<?= $category['id'] ?>" class="btn sm danger"
                           onclick="return confirm('Delete this category?');">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert_message error">No categories found.</div>
        <?php endif; ?>
    </main>
</div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

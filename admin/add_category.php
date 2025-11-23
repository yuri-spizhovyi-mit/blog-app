<?php
require_once __DIR__ . '/../partials/header.php';

// Restore form data if validation failed
$title = htmlspecialchars($_SESSION['add-category-data']['title'] ?? '');
$description = htmlspecialchars($_SESSION['add-category-data']['description'] ?? '');

unset($_SESSION['add-category-data']);
?>
<section class="form_section">
    <div class="container form_section-container">
        <h2>Add Category</h2>

        <?php if (isset($_SESSION['add-category-error'])): ?>
            <div class="alert_message error">
                <p><?= htmlspecialchars($_SESSION['add-category-error']); ?></p>
            </div>
            <?php unset($_SESSION['add-category-error']); ?>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>admin/add-category-logic.php" method="POST">
            <input type="text" 
                   name="title" 
                   value="<?= $title ?>" 
                   placeholder="Title"
                   required>

            <textarea rows="4" 
                      name="description" 
                      placeholder="Description"
                      required><?= $description ?></textarea>

            <button type="submit" name="submit" class="btn">Add Category</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

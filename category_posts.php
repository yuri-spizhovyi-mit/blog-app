<?php
require_once __DIR__ . '/partials/header.php';

/* ================================
   VALIDATE CATEGORY ID
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . ROOT_URL . 'blog.php');
    exit;
}
$category_id = intval($_GET['id']);

/* ================================
   FETCH CATEGORY
================================ */
$cat_stmt = $connection->prepare("SELECT title FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$category = $cat_stmt->get_result()->fetch_assoc();

if (!$category) {
    header('Location: ' . ROOT_URL . 'blog.php');
    exit;
}

/* ================================
   FETCH POSTS IN THIS CATEGORY
================================ */
$post_stmt = $connection->prepare("SELECT * FROM posts WHERE category_id = ? ORDER BY date_time DESC");
$post_stmt->bind_param("i", $category_id);
$post_stmt->execute();
$posts = $post_stmt->get_result();
?>

<header class="category_title">
    <h2><?= htmlspecialchars($category['title']) ?></h2>
</header>

<?php if ($posts->num_rows > 0): ?>
<section class="posts">
    <div class="container posts_container">
        <?php while ($post = $posts->fetch_assoc()) : ?>
        <article class="post">
            <div class="post_thumbnail">
                <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($post['thumbnail']) ?>">
            </div>

            <div class="post_info">

                <h3 class="post_title">
                    <a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h3>

                <p class="post_body">
                    <?= htmlspecialchars(substr($post['body'], 0, 150)) ?>...
                </p>

                <?php
                /* FETCH AUTHOR */
                $auth_stmt = $connection->prepare("SELECT firstname, lastname, avatar FROM users WHERE id = ?");
                $auth_stmt->bind_param("i", $post['author_id']);
                $auth_stmt->execute();
                $author = $auth_stmt->get_result()->fetch_assoc();

                $timestamp = strtotime($post['date_time']);
                $formattedDate = date("M d, Y - H:i", $timestamp);
                ?>

                <div class="post_author">
                    <div class="post_author-avatar">
                        <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($author['avatar']) ?>">
                    </div>
                    <div class="post_author-info">
                        <h5>By: <?= htmlspecialchars($author['firstname'] . " " . $author['lastname']) ?></h5>
                        <small><?= $formattedDate ?></small>
                    </div>
                </div>

            </div>
        </article>
        <?php endwhile; ?>
    </div>
</section>

<?php else: ?>
<div class="alert_message error lg">
    <p>No posts found for this category</p>
</div>
<?php endif; ?>

<section class="category_buttons">
    <div class="container category_button-container">
        <?php
        $cat_stmt = $connection->prepare("SELECT id, title FROM categories");
        $cat_stmt->execute();
        $all_cats = $cat_stmt->get_result();
        ?>

        <?php while ($c = $all_cats->fetch_assoc()) : ?>
            <a href="<?= ROOT_URL ?>category_posts.php?id=<?= $c['id'] ?>" 
               class="category_button"><?= htmlspecialchars($c['title']) ?></a>
        <?php endwhile; ?>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

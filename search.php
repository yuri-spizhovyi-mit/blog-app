<?php
require_once __DIR__ . '/partials/header.php';

/* ================================
   VALIDATE SEARCH INPUT
================================ */
if (!isset($_GET['search']) || trim($_GET['search']) === '') {
    header('Location: ' . ROOT_URL . 'blog.php');
    exit;
}

$search = trim($_GET['search']);
$search_like = '%' . $search . '%';

/* ================================
   SAFE SEARCH QUERY (PREPARED STATEMENT)
================================ */
$stmt = $connection->prepare("SELECT * FROM posts WHERE title LIKE ? OR body LIKE ? ORDER BY date_time DESC");
$stmt->bind_param("ss", $search_like, $search_like);
$stmt->execute();
$posts = $stmt->get_result();
?>

<?php if ($posts->num_rows > 0): ?>

<section class="posts section_extra-margin">
    <div class="container posts_container">

        <?php while ($post = $posts->fetch_assoc()): ?>
        <article class="post">

            <div class="post_thumbnail">
                <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($post['thumbnail']) ?>">
            </div>

            <div class="post_info">

                <?php
                /* Fetch category */
                $cat_stmt = $connection->prepare("SELECT id, title FROM categories WHERE id = ?");
                $cat_stmt->bind_param("i", $post['category_id']);
                $cat_stmt->execute();
                $category = $cat_stmt->get_result()->fetch_assoc();
                ?>

                <a class="category_button"
                   href="<?= ROOT_URL ?>category_posts.php?id=<?= $category['id'] ?>">
                   <?= htmlspecialchars($category['title']) ?>
                </a>

                <h3 class="post_title">
                    <a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h3>

                <p class="post_body">
                    <?= htmlspecialchars(substr($post['body'], 0, 150)) ?>...
                </p>

                <?php
                /* Fetch author */
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
                        <h5>By: <?= htmlspecialchars($author['firstname'] . ' ' . $author['lastname']) ?></h5>
                        <small><?= $formattedDate ?></small>
                    </div>
                </div>

            </div>
        </article>
        <?php endwhile; ?>

    </div>
</section>

<?php else: ?>

<div class="alert_message error lg section_extra-margin">
    <p>No posts found for this search</p>
</div>

<?php endif; ?>

<section class="category_buttons">
    <div class="container category_button-container">

        <?php
        $cat_stmt = $connection->prepare("SELECT id, title FROM categories");
        $cat_stmt->execute();
        $cats = $cat_stmt->get_result();
        ?>

        <?php while ($category = $cats->fetch_assoc()): ?>
            <a class="category_button"
               href="<?= ROOT_URL ?>category_posts.php?id=<?= $category['id'] ?>">
               <?= htmlspecialchars($category['title']) ?>
            </a>
        <?php endwhile; ?>

    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

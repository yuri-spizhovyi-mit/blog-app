<?php
require_once __DIR__ . '/partials/header.php';

// Validate and fetch post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . ROOT_URL . 'blog.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch post safely
$stmt = $connection->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    header('Location: ' . ROOT_URL . 'blog.php');
    exit;
}

/* Fetch author */
$auth_stmt = $connection->prepare("SELECT firstname, lastname, avatar FROM users WHERE id = ?");
$auth_stmt->bind_param("i", $post['author_id']);
$auth_stmt->execute();
$author = $auth_stmt->get_result()->fetch_assoc();

/* Fetch category */
$cat_stmt = $connection->prepare("SELECT title FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $post['category_id']);
$cat_stmt->execute();
$category = $cat_stmt->get_result()->fetch_assoc();

/* Time formatting */
$timestamp = strtotime($post['date_time']);
$current_time = time();
$diff = $current_time - $timestamp;

if ($diff < 60) $output = "Just now";
elseif ($diff < 3600) $output = floor($diff/60) . " minutes ago";
elseif ($diff < 86400) $output = floor($diff/3600) . " hours ago";
elseif ($diff < 604800) $output = floor($diff/86400) . " days ago";
else $output = date("M d, Y", $timestamp);

$formattedDate = date("M d, Y - H:i", $timestamp);
?>

<section class="singlepost">
    <div class="container singlepost_container">
        <h2><?= htmlspecialchars($post['title']) ?></h2>

        <div class="post_author">
            <div class="post_author-avatar">
                <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($author['avatar']) ?>">
            </div>
            <div class="post_author-info">
                <h5>By: <?= htmlspecialchars($author['firstname'] . ' ' . $author['lastname']) ?></h5>
                <small><?= $output ?><br><?= $formattedDate ?></small>
            </div>
        </div>

        <div class="singlepost_thumbnail">
            <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($post['thumbnail']) ?>">
        </div>

        <div class="post_info">
            <a href="<?= ROOT_URL ?>category_posts.php?id=<?= $post['category_id'] ?>" 
               class="category_button">
               <?= htmlspecialchars($category['title']) ?>
            </a>

            <p class="post_body">
                <?= nl2br(htmlspecialchars($post['body'])) ?>
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

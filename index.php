<?php
require_once __DIR__ . '/partials/header.php';

/* ================================
   FETCH FEATURED POST
================================ */
$featured_stmt = $connection->prepare("SELECT * FROM posts WHERE is_featured = 1 LIMIT 1");
$featured_stmt->execute();
$featured_result = $featured_stmt->get_result();
$featured = $featured_result->fetch_assoc();

/* ================================
   FETCH 9 LATEST POSTS
================================ */
$posts_stmt = $connection->prepare("SELECT * FROM posts ORDER BY date_time DESC LIMIT 9");
$posts_stmt->execute();
$posts = $posts_stmt->get_result();
?>

<?php if ($featured) : ?>
<section class="featured">
    <div class="container featured_container">
        <div class="post_thumbnail">
            <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($featured['thumbnail']) ?>">
        </div>

        <div class="post_info">
            <?php
            /* Fetch category */
            $cat_stmt = $connection->prepare("SELECT id, title FROM categories WHERE id = ?");
            $cat_stmt->bind_param("i", $featured['category_id']);
            $cat_stmt->execute();
            $category = $cat_stmt->get_result()->fetch_assoc();
            ?>

            <a class="category_button"
               href="<?= ROOT_URL ?>category_posts.php?id=<?= $category['id'] ?>">
               <?= htmlspecialchars($category['title']) ?>
            </a>

            <h2 class="post_title">
                <a href="<?= ROOT_URL ?>post.php?id=<?= $featured['id'] ?>">
                    <?= htmlspecialchars($featured['title']) ?>
                </a>
            </h2>

            <p class="post_body"><?= htmlspecialchars(substr($featured['body'], 0, 300)) ?>...</p>

            <?php
            /* Fetch author */
            $auth_stmt = $connection->prepare("SELECT firstname, lastname, avatar FROM users WHERE id = ?");
            $auth_stmt->bind_param("i", $featured['author_id']);
            $auth_stmt->execute();
            $author = $auth_stmt->get_result()->fetch_assoc();

            $timestamp = strtotime($featured['date_time']);
            $current_time = time();
            $time_diff = $current_time - $timestamp;
            if ($time_diff < 60) $output = "Just now";
            elseif ($time_diff < 3600) $output = floor($time_diff/60) . " minutes ago";
            elseif ($time_diff < 86400) $output = floor($time_diff/3600) . " hours ago";
            elseif ($time_diff < 604800) $output = floor($time_diff/86400) . " days ago";
            else $output = date("M d, Y", $timestamp);

            $formattedDate = date("M d, Y - H:i", $timestamp);
            ?>

            <div class="post_author">
                <div class="post_author-avatar">
                    <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($author['avatar']) ?>">
                </div>

                <div class="post_author-info">
                    <h5>By: <?= htmlspecialchars($author['firstname'] . " " . $author['lastname']) ?></h5>
                    <small><?= $output ?><br><?= $formattedDate ?></small>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="posts <?= $featured ? '' : 'section_extra-margin' ?>">
    <div class="container posts_container">
        <?php while ($post = $posts->fetch_assoc()) : ?>
        <article class="post">
            <div class="post_thumbnail">
                <img src="<?= ROOT_URL . 'images/' . htmlspecialchars($post['thumbnail']) ?>">
            </div>

            <div class="post_info">
                <?php
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

                <p class="post_body"><?= htmlspecialchars(substr($post['body'], 0, 150)) ?>...</p>

                <?php
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

<section class="category_buttons">
    <div class="container category_button-container">
        <?php
        $cat_stmt = $connection->prepare("SELECT id, title FROM categories");
        $cat_stmt->execute();
        $categories = $cat_stmt->get_result();
        ?>

        <?php while ($category = $categories->fetch_assoc()) : ?>
        <a class="category_button"
           href="<?= ROOT_URL ?>category_posts.php?id=<?= $category['id'] ?>">
            <?= htmlspecialchars($category['title']) ?>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

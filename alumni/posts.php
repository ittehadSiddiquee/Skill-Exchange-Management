<?php
require_once '../../config/db_connect.php';
require_once '../../models/alumni_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'alumni') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$alumniId = get_alumni_id($conn, $userId);
$myPosts = get_my_posts($conn, $alumniId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Posts - Alumni</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="posts.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="posts.php" class="active">My Posts</a>
            <a href="requests.php">Requests</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>My Posts</h1>
            <p class="welcome-text">Share industry tips and career insights with current students.</p>

            <?php
            if (isset($_SESSION['post_success'])) {
                echo '<div class="success-box">' . $_SESSION['post_success'] . '</div>';
                unset($_SESSION['post_success']);
            }
            if (isset($_SESSION['post_errors'])) {
                echo '<div class="error-box">';
                foreach ($_SESSION['post_errors'] as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
                unset($_SESSION['post_errors']);
            }
            ?>

            
            <div class="new-post-box">
                <h3>Write a New Post</h3>
                <form action="../../controllers/alumni_controller.php" method="POST">
                    <input type="hidden" name="action" value="create_post">

                    <label>Title</label>
                    <input type="text" name="title" placeholder="e.g. 5 Tips for Your First Tech Interview" required>

                    <label>Content</label>
                    <textarea name="content" rows="5" placeholder="Write your industry tip or advice here..." required></textarea>

                    <label>Related Link (optional)</label>
                    <input type="text" name="link" placeholder="https://... (article, job posting, resource, etc.)">

                    <button type="submit">Publish Post</button>
                </form>
            </div>

            
            <h3 class="section-title">Your Published Posts (<?php echo count($myPosts); ?>)</h3>

            <?php if (count($myPosts) == 0) { ?>
                <p class="empty-text">You haven't published any posts yet.</p>
            <?php } else { ?>

                <div class="post-list">
                    <?php foreach ($myPosts as $post) { ?>
                        <div class="post-card">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="post-date"><?php echo htmlspecialchars($post['createdAt']); ?></p>
                            <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if (!empty($post['link'])) { ?>
                                <p class="post-link">
                                    <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($post['link']); ?>
                                    </a>
                                </p>
                            <?php } ?>

                            <form action="../../controllers/alumni_controller.php" method="POST"
                                  onsubmit="return confirm('Delete this post? This cannot be undone.');">
                                <input type="hidden" name="action" value="delete_post">
                                <input type="hidden" name="postId" value="<?php echo $post['postId']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>

        </div>

    </div>

</body>
</html>
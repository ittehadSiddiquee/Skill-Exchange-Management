<?php
require_once '../../config/db_connect.php';
require_once '../../models/student_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}


$keyword = "";
if (isset($_GET['keyword']) && trim($_GET['keyword']) != "") {
    $keyword = trim($_GET['keyword']);
    $alumniList = search_alumni($conn, $keyword);
} else {
    $alumniList = get_all_approved_alumni($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Alumni - Student</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="find_alumni.css">
</head>
<body>

    <div class="layout">

      
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="find_mentors.php">Find Mentors</a>
            <a href="find_alumni.php" class="active">Find Alumni</a>
            <a href="my_requests.php">My Requests</a>
            <a href="skills.php">My Skills</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>Find Alumni</h1>
            <p class="welcome-text">Browse alumni profiles and read their industry tips and posts.</p>

            <?php
            if (isset($_SESSION['request_success'])) {
                echo '<div class="success-box">' . $_SESSION['request_success'] . '</div>';
                unset($_SESSION['request_success']);
            }
            if (isset($_SESSION['request_errors'])) {
                echo '<div class="error-box">';
                foreach ($_SESSION['request_errors'] as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
                unset($_SESSION['request_errors']);
            }
            ?>

          
            <form action="find_alumni.php" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="e.g. Software, CSE, Rahman..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit">Search</button>
            </form>

            
            <?php if (count($alumniList) == 0) { ?>
                <p class="empty-text">No alumni found. Try a different search term.</p>
            <?php } else { ?>

                <div class="alumni-list">
                    <?php foreach ($alumniList as $alumni) {
                        $posts = get_posts_by_alumni($conn, $alumni['alumniId']);
                    ?>
                        <div class="alumni-card">
                            <h3><?php echo htmlspecialchars($alumni['fullName']); ?></h3>
                            <p class="alumni-dept"><?php echo htmlspecialchars($alumni['department']); ?></p>
                            <p><strong>Graduated:</strong> <?php echo htmlspecialchars($alumni['graduationYear']); ?></p>
                            <p><strong>Company:</strong> <?php echo htmlspecialchars($alumni['company']); ?></p>
                            <p><strong>Industry:</strong> <?php echo htmlspecialchars($alumni['industry']); ?></p>
                            <?php if (!empty($alumni['bio'])) { ?>
                                <p class="alumni-bio"><?php echo htmlspecialchars($alumni['bio']); ?></p>
                            <?php } ?>
                            <?php if (!empty($alumni['linkedin'])) { ?>
                                <p><a href="<?php echo htmlspecialchars($alumni['linkedin']); ?>" target="_blank" class="linkedin-link">LinkedIn Profile</a></p>
                            <?php } ?>

                            <div class="alumni-actions">
                                <button class="view-posts-btn" onclick="togglePosts(<?php echo $alumni['alumniId']; ?>)">
                                    View Posts (<?php echo count($posts); ?>)
                                </button>
                                <button class="send-request-btn" onclick="toggleRequestForm(<?php echo $alumni['userId']; ?>)">
                                    Send Request
                                </button>
                            </div>

                            
                            <form action="../../controllers/student_controller.php" method="POST"
                                  id="request-form-<?php echo $alumni['userId']; ?>" class="request-form" style="display:none;">
                                <input type="hidden" name="action" value="send_request">
                                <input type="hidden" name="receiverId" value="<?php echo $alumni['userId']; ?>">
                                <input type="hidden" name="returnPage" value="find_alumni.php">

                                <label>Message</label>
                                <textarea name="message" rows="3" placeholder="e.g. Could you share advice on breaking into your industry?" required></textarea>

                                <label>Preferred Time</label>
                                <input type="text" name="preferredTime" placeholder="e.g. Weekday evenings">

                                <button type="submit" class="confirm-send-btn">Confirm & Send</button>
                            </form>

                            <div id="posts-<?php echo $alumni['alumniId']; ?>" class="posts-section" style="display:none;">
                                <?php if (count($posts) == 0) { ?>
                                    <p class="no-posts">This alumni hasn't published any posts yet.</p>
                                <?php } else { ?>
                                    <?php foreach ($posts as $post) { ?>
                                        <div class="post-item">
                                            <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                                            <p class="post-date"><?php echo htmlspecialchars($post['createdAt']); ?></p>
                                            <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                            <?php if (!empty($post['link'])) { ?>
                                                <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" class="post-link">
                                                    <?php echo htmlspecialchars($post['link']); ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>

        </div>

    </div>

    <script src="find_alumni.js"></script>
</body>
</html>
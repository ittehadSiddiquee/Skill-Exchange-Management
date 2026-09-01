<?php
require_once '../../config/db_connect.php';
require_once '../../models/admin_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$allAnnouncements = get_all_announcements($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements - Admin</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="announcements.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="approvals.php">Approvals</a>
            <a href="users.php">All Users</a>
            <a href="announcements.php" class="active">Announcements</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
        <h1>Announcements</h1>
        <p class="welcome-text">Send a platform-wide message to all users, or target a specific role.</p>

        <?php
        if (isset($_SESSION['announcement_success'])) {
        echo '<div class="success-box">' . $_SESSION['announcement_success'] . '</div>';
        unset($_SESSION['announcement_success']);
            }
            if (isset($_SESSION['announcement_errors'])) {
                echo '<div class="error-box">';
                foreach ($_SESSION['announcement_errors'] as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
                unset($_SESSION['announcement_errors']);
            }
            ?>

            
            <div class="new-announcement-box">
            <h3>New Announcement</h3>
            <form action="../../controllers/admin_controller.php" method="POST">
            <input type="hidden" name="action" value="send_announcement">

            <label>Send To</label>
         <select name="targetRole">
         <option value="all">Everyone</option>
            <option value="student">Students Only</option>
             <option value="mentor">Mentors Only</option>
            <option value="alumni">Alumni Only</option>
        </select>

        <label>Message</label>
        <textarea name="message" rows="4" placeholder="Write your announcement here..." required></textarea>

        <button type="submit">Send Announcement</button>
        </form>
        </div>

            
    <h3 class="section-title">Sent Announcements (<?php echo count($allAnnouncements); ?>)</h3>

    <?php if (count($allAnnouncements) == 0) { ?>
    <p class="empty-text">No announcements sent yet.</p>
    <?php } else { ?>

    <div class="announcement-list">
    <?php foreach ($allAnnouncements as $a) { ?>
    <div class="announcement-card">
    <div class="announcement-top">
    <span class="target-badge target-<?php echo $a['targetRole']; ?>">
    <?php echo ($a['targetRole'] == 'all') ? 'Everyone' : ucfirst($a['targetRole']) . 's Only'; ?>
    </span>
<span class="announcement-date"><?php echo htmlspecialchars($a['sentAt']); ?></span>
</div>
        <p class="announcement-message"><?php echo nl2br(htmlspecialchars($a['message'])); ?></p>
    <p class="announcement-sender">Sent by <?php echo htmlspecialchars($a['sentBy']); ?></p>
     </div>
    <?php } ?>
        </div>

    <?php } ?>

    </div>

    </div>

</body>
</html>
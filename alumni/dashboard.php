<?php
require_once '../../config/db_connect.php';
require_once '../../models/alumni_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'alumni') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$profile = get_alumni_full_profile($conn, $userId);
$announcements = get_announcements_for_role($conn, 'alumni');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="announcement_box.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="posts.php">My Posts</a>
            <a href="requests.php">Requests</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>Alumni Dashboard</h1>
            <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($profile['fullName']); ?>!</p>

            <div class="info-card">
                <h3>Your Info</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($profile['department']); ?></p>
                <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($profile['graduationYear']); ?></p>
                <p><strong>Company:</strong> <?php echo htmlspecialchars($profile['company']); ?></p>
                <p><strong>Approval Status:</strong>
                    <?php echo ($profile['isApproved'] == 1) ? "Approved" : "Pending Admin Approval"; ?>
                </p>
            </div>

            <p class="hint-text">Go to <a href="profile.php">My Profile</a> to update your information.</p>

            
            <div class="announcement-box">
                <h3>📢 Recent Announcements</h3>
                <?php if (count($announcements) == 0) { ?>
                    <p class="no-announcements">No announcements right now.</p>
                <?php } else { ?>
                    <?php foreach ($announcements as $a) { ?>
                        <div class="announcement-item">
                            <p><?php echo htmlspecialchars($a['message']); ?></p>
                            <span><?php echo htmlspecialchars($a['sentAt']); ?></span>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

    </div>

    <script src="dashboard.js"></script>
</body>
</html>
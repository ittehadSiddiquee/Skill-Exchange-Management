<?php
require_once '../../config/db_connect.php';
require_once '../../models/admin_model.php';

// --- Protect this page: only logged-in admins can see it ---
if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$profile = get_admin_full_profile($conn, $userId);

// Summary stats for the dashboard cards
$totalUsers = count_total_users($conn);
$pendingMentors = count_pending_mentors($conn);
$pendingAlumni = count_pending_alumni($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <div class="layout">

        <!-- ===== Sidebar ===== -->
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="approvals.php">Approvals</a>
            <a href="users.php">All Users</a>
            <a href="announcements.php">Announcements</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        <!-- ===== Main content ===== -->
        <div class="main-content">
            <h1>Admin Dashboard</h1>
            <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($profile['fullName']); ?>!</p>

            <!-- ===== Summary cards ===== -->
            <div class="stats-row">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalUsers; ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
                <a href="approvals.php" class="stat-card-link">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $pendingMentors; ?></span>
                        <span class="stat-label">Pending Mentors</span>
                    </div>
                </a>
                <a href="approvals.php" class="stat-card-link">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $pendingAlumni; ?></span>
                        <span class="stat-label">Pending Alumni</span>
                    </div>
                </a>
            </div>

            <div class="info-card">
                <h3>Your Info</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($profile['department']); ?></p>
                <p><strong>Admin Level:</strong> <?php echo htmlspecialchars($profile['adminLevel']); ?></p>
            </div>

            <p class="hint-text">Go to <a href="profile.php">My Profile</a> to update your information.</p>
            <p class="hint-text">(User management and approval actions will be added as a future step.)</p>
        </div>

    </div>

    <script src="dashboard.js"></script>
</body>
</html>
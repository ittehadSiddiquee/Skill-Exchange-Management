<?php
require_once '../../config/db_connect.php';
require_once '../../models/student_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$myRequests = get_my_sent_requests($conn, $userId);


$pendingCount = 0;
$acceptedCount = 0;
$rejectedCount = 0;

foreach ($myRequests as $req) {
    if ($req['status'] == 'pending') { $pendingCount++; }
    elseif ($req['status'] == 'accepted') { $acceptedCount++; }
    elseif ($req['status'] == 'rejected') { $rejectedCount++; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests - Student</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="my_requests.css">
</head>
<body>

    <div class="layout">

       
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="find_mentors.php">Find Mentors</a>
            <a href="find_alumni.php">Find Alumni</a>
            <a href="my_requests.php" class="active">My Requests</a>
            <a href="skills.php">My Skills</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>My Requests</h1>
            <p class="welcome-text">Track the status of requests you've sent to mentors.</p>

          
            <div class="stats-row">
                <div class="stat-card">
                    <span class="stat-number"><?php echo count($myRequests); ?></span>
                    <span class="stat-label">Total Sent</span>
                </div>
                <div class="stat-card pending-card">
                    <span class="stat-number"><?php echo $pendingCount; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
                <div class="stat-card accepted-card">
                    <span class="stat-number"><?php echo $acceptedCount; ?></span>
                    <span class="stat-label">Accepted</span>
                </div>
                <div class="stat-card rejected-card">
                    <span class="stat-number"><?php echo $rejectedCount; ?></span>
                    <span class="stat-label">Rejected</span>
                </div>
            </div>

          
            <?php if (count($myRequests) == 0) { ?>
                <p class="empty-text">You haven't sent any requests yet. Go to <a href="find_mentors.php">Find Mentors</a> to get started.</p>
            <?php } else { ?>

                <div class="request-list">
                    <?php foreach ($myRequests as $req) { ?>
                        <div class="request-card">
                            <div class="request-top">
                                <h3><?php echo htmlspecialchars($req['mentorName']); ?></h3>
                                <span class="status-badge status-<?php echo $req['status']; ?>">
                                    <?php echo ucfirst($req['status']); ?>
                                </span>
                            </div>
                            <p class="request-meta"><?php echo htmlspecialchars($req['mentorDepartment']); ?></p>
                            <p class="request-message"><?php echo htmlspecialchars($req['message']); ?></p>
                            <?php if (!empty($req['preferredTime'])) { ?>
                                <p><strong>Preferred Time:</strong> <?php echo htmlspecialchars($req['preferredTime']); ?></p>
                            <?php } ?>
                            <p class="request-date">Sent on <?php echo htmlspecialchars($req['createdAt']); ?></p>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>

        </div>

    </div>

</body>
</html>
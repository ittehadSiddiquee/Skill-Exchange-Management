<?php
require_once '../../config/db_connect.php';
require_once '../../models/mentor_model.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'mentor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$pendingRequests = get_pending_requests_for_mentor($conn, $userId);
$respondedRequests = get_responded_requests_for_mentor($conn, $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Requests - Mentor</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="requests.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="requests.php" class="active">Requests</a>
            <a href="find_alumni.php">Find Alumni</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        <!-- ===== Main content ===== -->
        <div class="main-content">
            <h1>Manage Requests</h1>
            <p class="welcome-text">Review requests from students and accept or reject them.</p>

            <?php
            if (isset($_SESSION['request_action_success'])) {
                echo '<div class="success-box">' . $_SESSION['request_action_success'] . '</div>';
                unset($_SESSION['request_action_success']);
            }
            ?>

            
            <h3 class="section-title">Pending Requests (<?php echo count($pendingRequests); ?>)</h3>

            <?php if (count($pendingRequests) == 0) { ?>
                <p class="empty-text">No pending requests right now.</p>
            <?php } else { ?>

                <div class="request-list">
                    <?php foreach ($pendingRequests as $req) { ?>
                        <div class="request-card">
                            <h3><?php echo htmlspecialchars($req['fullName']); ?></h3>
                            <p class="request-meta"><?php echo htmlspecialchars($req['department']); ?> &middot; <?php echo htmlspecialchars($req['email']); ?></p>
                            <p class="request-message"><?php echo htmlspecialchars($req['message']); ?></p>
                            <?php if (!empty($req['preferredTime'])) { ?>
                                <p><strong>Preferred Time:</strong> <?php echo htmlspecialchars($req['preferredTime']); ?></p>
                            <?php } ?>
                            <p class="request-date">Requested on <?php echo htmlspecialchars($req['createdAt']); ?></p>

                            <div class="request-actions">
                                <form action="../../controllers/mentor_controller.php" method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="accept_request">
                                    <input type="hidden" name="requestId" value="<?php echo $req['requestId']; ?>">
                                    <button type="submit" class="accept-btn">Accept</button>
                                </form>
                                <form action="../../controllers/mentor_controller.php" method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="reject_request">
                                    <input type="hidden" name="requestId" value="<?php echo $req['requestId']; ?>">
                                    <button type="submit" class="decline-btn">Decline</button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>

            <h3 class="section-title">Request History</h3>

            <?php if (count($respondedRequests) == 0) { ?>
                <p class="empty-text">No history yet.</p>
            <?php } else { ?>
                <table class="history-table">
                    <tr>
                        <th>Student</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    <?php foreach ($respondedRequests as $req) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($req['fullName']); ?></td>
                            <td><?php echo htmlspecialchars($req['message']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $req['status']; ?>">
                                    <?php echo ucfirst($req['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($req['createdAt']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>

        </div>

    </div>

</body>
</html>
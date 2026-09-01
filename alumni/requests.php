<?php
require_once '../../config/db_connect.php';
require_once '../../models/alumni_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'alumni') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$pendingRequests = get_pending_requests_for_alumni($conn, $userId);
$respondedRequests = get_responded_requests_for_alumni($conn, $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Requests - Alumni</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="requests.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="posts.php">My Posts</a>
            <a href="requests.php" class="active">Requests</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>Manage Requests</h1>
            <p class="welcome-text">Review requests from students and mentors, and accept or reject them.</p>

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
                            <h3><?php echo htmlspecialchars($req['fullName']); ?>
                                <span class="role-tag role-tag-<?php echo $req['role']; ?>"><?php echo ucfirst($req['role']); ?></span>
                            </h3>
                            <p class="request-meta"><?php echo htmlspecialchars($req['department']); ?> &middot; <?php echo htmlspecialchars($req['email']); ?></p>
                            <p class="request-message"><?php echo htmlspecialchars($req['message']); ?></p>
                            <?php if (!empty($req['preferredTime'])) { ?>
                                <p><strong>Preferred Time:</strong> <?php echo htmlspecialchars($req['preferredTime']); ?></p>
                            <?php } ?>
                            <p class="request-date">Requested on <?php echo htmlspecialchars($req['createdAt']); ?></p>

                            <div class="request-actions">
                                <form action="../../controllers/alumni_controller.php" method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="accept_request">
                                    <input type="hidden" name="requestId" value="<?php echo $req['requestId']; ?>">
                                    <button type="submit" class="accept-btn">Accept</button>
                                </form>
                                <form action="../../controllers/alumni_controller.php" method="POST" class="inline-form">
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
                        <th>Name</th>
                        <th>Role</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    <?php foreach ($respondedRequests as $req) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($req['fullName']); ?></td>
                            <td><?php echo ucfirst($req['role']); ?></td>
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
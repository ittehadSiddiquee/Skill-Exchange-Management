<?php
require_once '../../config/db_connect.php';
require_once '../../models/admin_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$pendingMentors = get_pending_mentors($conn);
$pendingAlumni = get_pending_alumni($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approvals - Admin</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="approvals.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="approvals.php" class="active">Approvals</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>Pending Approvals</h1>
            <p class="welcome-text">Review and approve or reject new Mentor and Alumni applications.</p>

            <?php
            if (isset($_SESSION['approval_success'])) {
                echo '<div class="success-box">' . $_SESSION['approval_success'] . '</div>';
                unset($_SESSION['approval_success']);
            }
            ?>

            
        <h3 class="section-title">Pending Mentors (<?php echo count($pendingMentors); ?>)</h3>

        <?php if (count($pendingMentors) == 0) { ?>
                <p class="empty-text">No pending mentor applications right now.</p>
        <?php } else { ?>
        <table class="approval-table">
        <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Department</th>
        <th>Expertise</th>
        <th>Action</th>
        </tr>
    <?php foreach ($pendingMentors as $mentor) { ?>
    <tr>
    <td><?php echo htmlspecialchars($mentor['fullName']); ?></td>
    <td><?php echo htmlspecialchars($mentor['email']); ?></td>
    <td><?php echo htmlspecialchars($mentor['department']); ?></td>
    <td><?php echo htmlspecialchars($mentor['expertise']); ?></td>
    <td class="action-cell">
    <form action="../../controllers/admin_controller.php" method="POST" class="inline-form">
    <input type="hidden" name="action" value="approve_mentor">
    <input type="hidden" name="mentorId" value="<?php echo $mentor['mentorId']; ?>">
    <button type="submit" class="approve-btn">Approve</button>
    </form>
    <form action="../../controllers/admin_controller.php" method="POST" class="inline-form" onsubmit="return confirm('Reject this mentor application? This will delete their account.');">
    <input type="hidden" name="action" value="reject_mentor">
     <input type="hidden" name="mentorId" value="<?php echo $mentor['mentorId']; ?>">
    <input type="hidden" name="userId" value="<?php echo $mentor['userId']; ?>">
    <button type="submit" class="reject-btn">Reject</button>
    </form>
    </td>
        </tr>
        <?php } ?>
    </table>
            <?php } ?>

           
    <h3 class="section-title">Pending Alumni (<?php echo count($pendingAlumni); ?>)</h3>

    <?php if (count($pendingAlumni) == 0) { ?>
                <p class="empty-text">No pending alumni applications right now.</p>
    <?php } else { ?>
    <table class="approval-table">
        <tr>
     <th>Name</th>
    <th>Email</th>
    <th>Department</th>
    <th>Graduation Year</th>
    <th>Company</th>
    <th>Action</th>
        </tr>
    <?php foreach ($pendingAlumni as $alumni) { ?>
        <tr>
    <td><?php echo htmlspecialchars($alumni['fullName']); ?></td>
 <td><?php echo htmlspecialchars($alumni['email']); ?></td>
<td><?php echo htmlspecialchars($alumni['department']); ?></td>
    <td><?php echo htmlspecialchars($alumni['graduationYear']); ?></td>
<td><?php echo htmlspecialchars($alumni['company']); ?></td>
     <td class="action-cell">
    <form action="../../controllers/admin_controller.php" method="POST" class="inline-form">
    <input type="hidden" name="action" value="approve_alumni">
    <input type="hidden" name="alumniId" value="<?php echo $alumni['alumniId']; ?>">
    <button type="submit" class="approve-btn">Approve</button>
        </form>
    <form action="../../controllers/admin_controller.php" method="POST" class="inline-form" onsubmit="return confirm('Reject this alumni application? This will delete their account.');">
        <input type="hidden" name="action" value="reject_alumni">
    <input type="hidden" name="alumniId" value="<?php echo $alumni['alumniId']; ?>">
     <input type="hidden" name="userId" value="<?php echo $alumni['userId']; ?>">
    <button type="submit" class="reject-btn">Reject</button>
    </form>
    </td>
    </tr>
        <?php } ?>
    </table>
    <?php } ?>

</div>

    </div>

</body>
</html>
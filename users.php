<?php
require_once '../../config/db_connect.php';
require_once '../../models/admin_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


$roleFilter = isset($_GET['role']) ? $_GET['role'] : 'all';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$users = get_all_users($conn, $roleFilter, $keyword);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Users - Admin</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="approvals.css">
    <link rel="stylesheet" href="users.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="approvals.php">Approvals</a>
            <a href="users.php" class="active">All Users</a>
            <a href="announcements.php">Announcements</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>All Users</h1>
            <p class="welcome-text">View and manage every account on the platform.</p>

            <?php
            if (isset($_SESSION['users_success'])) {
                echo '<div class="success-box">' . $_SESSION['users_success'] . '</div>';
                unset($_SESSION['users_success']);
            }
            ?>

            
            <form action="users.php" method="GET" class="filter-form">
                <select name="role">
                <option value="all" <?php echo ($roleFilter == 'all') ? 'selected' : ''; ?>>All Roles</option>
                <option value="student" <?php echo ($roleFilter == 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="mentor" <?php echo ($roleFilter == 'mentor') ? 'selected' : ''; ?>>Mentor</option>
                <option value="alumni" <?php echo ($roleFilter == 'alumni') ? 'selected' : ''; ?>>Alumni</option>
                <option value="admin" <?php echo ($roleFilter == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>

            <input type="text" name="keyword" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($keyword); ?>">

            <button type="submit">Filter</button>
            </form>

            <p class="result-count"><?php echo count($users); ?> user(s) found</p>

            <?php if (count($users) == 0) { ?>
                <p class="empty-text">No users match this search.</p>
            <?php } else { ?>
                <table class="approval-table">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($users as $u) { ?>
                        <tr>
                <td><?php echo htmlspecialchars($u['fullName']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['department']); ?></td>
                <td><span class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                <td>
                <?php if ($u['isActive'] == 1) { ?>
                <span class="status-badge status-active">Active</span>
                <?php } else { ?>
                <span class="status-badge status-inactive">Inactive</span>
                <?php } ?>
                </td>
                <td><?php echo htmlspecialchars($u['createdAt']); ?></td>
                <td>
                <?php if ($u['role'] != 'admin') { ?>
                <form action="../../controllers/admin_controller.php" method="POST" class="inline-form">
                <input type="hidden" name="action" value="toggle_active">
        <input type="hidden" name="userId" value="<?php echo $u['userId']; ?>">
            <?php if ($u['isActive'] == 1) { ?>
        <input type="hidden" name="newStatus" value="0">
        <button type="submit" class="reject-btn">Deactivate</button>
        <?php } else { ?>
        <input type="hidden" name="newStatus" value="1">
        <button type="submit" class="approve-btn">Activate</button>
         <?php } ?>
        </form>
                    <?php } else { ?>
       <span class="no-action">—</span>
        <?php } ?>
         </td>
         </tr>
         <?php } ?>
            </table>
            <?php } ?>

    </div>

    </div>

</body>
</html>
<?php
require_once '../../config/db_connect.php';
require_once '../../models/admin_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$profile = get_admin_full_profile($conn, $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Admin</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="approvals.php">Approvals</a>
            <a href="users.php">All Users</a>
            <a href="announcements.php">Announcements</a>
            <a href="profile.php" class="active">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>My Profile</h1>
            <p class="subtitle">Update your admin account information</p>

            <?php
            if (isset($_SESSION['profile_success'])) {
                echo '<div class="success-box">' . $_SESSION['profile_success'] . '</div>';
                unset($_SESSION['profile_success']);
            }

            if (isset($_SESSION['profile_errors'])) {
                echo '<div class="error-box">';
                foreach ($_SESSION['profile_errors'] as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
                unset($_SESSION['profile_errors']);
            }
        ?>

    <form action="../../controllers/admin_controller.php" method="POST" class="profile-form">
         <input type="hidden" name="action" value="update_profile">

        <h3>Basic Information</h3>

          <label>Full Name</label>
         <input type="text" name="fullName" value="<?php echo htmlspecialchars($profile['fullName']); ?>" required>

        <label>Email (cannot be changed)</label>
         <input type="email" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled>

        <label>Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($profile['phone']); ?>">

        <label>Department</label>
    <input type="text" name="department" value="<?php echo htmlspecialchars($profile['department']); ?>" required>

        <h3>Admin Information</h3>

         <label>Admin Level</label>
        <select name="adminLevel">
        <option value="standard" <?php echo ($profile['adminLevel'] == 'standard') ? 'selected' : ''; ?>>Standard</option>
        <option value="super" <?php echo ($profile['adminLevel'] == 'super') ? 'selected' : ''; ?>>Super Admin</option>
        </select>

        <h3>About You</h3>

        <label>Bio</label>
        <textarea name="bio" rows="3" placeholder="A short description about yourself..."><?php echo htmlspecialchars($profile['bio']); ?></textarea>

        <label>Interests</label>
    <textarea name="interests" rows="2" placeholder="Optional"><?php echo htmlspecialchars($profile['interests']); ?></textarea>

        <label>LinkedIn URL</label>
  <input type="text" name="linkedin" value="<?php echo htmlspecialchars($profile['linkedin']); ?>" placeholder="https://linkedin.com/in/yourname">

        <label>Profile Picture URL</label>
    <input type="text" name="profilePicture" value="<?php echo htmlspecialchars($profile['profilePicture']); ?>" placeholder="Link to your photo (optional)">

    <button type="submit">Save Changes</button>
    </form>
 </div>

 </div>

    <script src="profile.js"></script>
</body>
</html>
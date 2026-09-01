<?php
require_once '../../config/db_connect.php';
require_once '../../models/mentor_model.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'mentor') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$profile = get_mentor_full_profile($conn, $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Mentor</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>

    <div class="layout">

        
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="requests.php">Requests</a>
            <a href="find_alumni.php">Find Alumni</a>
            <a href="profile.php" class="active">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        <div class="main-content">
            <h1>My Profile</h1>
            <p class="subtitle">Update your mentor information</p>

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

            <form action="../../controllers/mentor_controller.php" method="POST" class="profile-form">
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

                <h3>Mentor Information</h3>

                <label>Expertise</label>
                <input type="text" name="expertise" value="<?php echo htmlspecialchars($profile['expertise']); ?>" placeholder="e.g. Web Development, Python, UI/UX" required>

                <label>Experience</label>
                <input type="text" name="experience" value="<?php echo htmlspecialchars($profile['experience']); ?>" placeholder="e.g. 3 years">

                <label>Availability</label>
                <input type="text" name="availability" value="<?php echo htmlspecialchars($profile['availability']); ?>" placeholder="e.g. Weekdays 5-7 PM">

                <h3>About You</h3>

                <label>Bio</label>
                <textarea name="bio" rows="3" placeholder="Tell students a bit about yourself..."><?php echo htmlspecialchars($profile['bio']); ?></textarea>

                <label>Interests / Skills</label>
                <textarea name="interests" rows="2" placeholder="e.g. Machine Learning, Public Speaking"><?php echo htmlspecialchars($profile['interests']); ?></textarea>

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
<?php
require_once '../../config/db_connect.php';
require_once '../../models/student_model.php';

// --- Protect this page: only logged-in students can see it ---
if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];

// --- Handle the search ---
// If a keyword was typed (via GET, since it's a search, not a data change),
// search for matching mentors. Otherwise show everyone.
$keyword = "";
if (isset($_GET['keyword']) && trim($_GET['keyword']) != "") {
    $keyword = trim($_GET['keyword']);
    $mentors = search_mentors($conn, $keyword);
} else {
    $mentors = get_all_approved_mentors($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Mentors - Student</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="find_mentors.css">
</head>
<body>

    <div class="layout">

       
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="find_mentors.php" class="active">Find Mentors</a>
            <a href="find_alumni.php">Find Alumni</a>
            <a href="my_requests.php">My Requests</a>
            <a href="skills.php">My Skills</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

       
        <div class="main-content">
            <h1>Find Mentors</h1>
            <p class="welcome-text">Search by name, department, or skill/expertise</p>

            <?php
            if (isset($_SESSION['request_success'])) {
                echo '<div class="success-box">' . $_SESSION['request_success'] . '</div>';
                unset($_SESSION['request_success']);
            }
            if (isset($_SESSION['request_errors'])) {
                echo '<div class="error-box">';
                foreach ($_SESSION['request_errors'] as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
                unset($_SESSION['request_errors']);
            }
            ?>

            
            <form action="find_mentors.php" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="e.g. Python, CSE, Rahman..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit">Search</button>
            </form>

            
            <?php if (count($mentors) == 0) { ?>
                <p class="empty-text">No mentors found. Try a different search term.</p>
            <?php } else { ?>

                <div class="mentor-list">
                    <?php foreach ($mentors as $mentor) { ?>
                        <div class="mentor-card">
                            <div class="mentor-info">
                                <h3><?php echo htmlspecialchars($mentor['fullName']); ?></h3>
                                <p class="mentor-dept"><?php echo htmlspecialchars($mentor['department']); ?></p>
                                <p><strong>Expertise:</strong> <?php echo htmlspecialchars($mentor['expertise']); ?></p>
                                <p><strong>Experience:</strong> <?php echo htmlspecialchars($mentor['experience']); ?></p>
                                <p><strong>Availability:</strong> <?php echo htmlspecialchars($mentor['availability']); ?></p>
                                <?php if (!empty($mentor['bio'])) { ?>
                                    <p class="mentor-bio"><?php echo htmlspecialchars($mentor['bio']); ?></p>
                                <?php } ?>
                            </div>

                            <button class="send-request-btn" onclick="toggleRequestForm(<?php echo $mentor['userId']; ?>)">
                                Send Request
                            </button>

                            
                            <form action="../../controllers/student_controller.php" method="POST"
                                  id="request-form-<?php echo $mentor['userId']; ?>" class="request-form" style="display:none;">
                                <input type="hidden" name="action" value="send_request">
                                <input type="hidden" name="receiverId" value="<?php echo $mentor['userId']; ?>">

                                <label>Message</label>
                                <textarea name="message" rows="3" placeholder="What do you need help with?" required></textarea>

                                <label>Preferred Time</label>
                                <input type="text" name="preferredTime" placeholder="e.g. Weekday afternoons">

                                <button type="submit" class="confirm-send-btn">Confirm & Send</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>

        </div>

    </div>

    <script src="find_mentors.js"></script>
</body>
</html>
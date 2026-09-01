<?php
require_once '../../config/db_connect.php';
require_once '../../models/student_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];
$profileId = get_profile_id($conn, $userId);
$mySkills = get_my_skills($conn, $profileId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Skills - Student</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="skills.css">
</head>
<body>

    <div class="layout">

       
        <div class="sidebar">
            <h2>Campus Skill<br>Exchange</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="find_mentors.php">Find Mentors</a>
            <a href="find_alumni.php">Find Alumni</a>
            <a href="my_requests.php">My Requests</a>
            <a href="skills.php" class="active">My Skills</a>
            <a href="profile.php">My Profile</a>
            <a href="../../logout.php" class="logout-link">Logout</a>
        </div>

        
        <div class="main-content">
            <h1>My Skills</h1>
            <p class="welcome-text">Add the skills you have so mentors and peers can find you.</p>

            <?php
            if (isset($_SESSION['skill_success'])) {
                echo '<div class="success-box">' . $_SESSION['skill_success'] . '</div>';
                unset($_SESSION['skill_success']);
            }
            if (isset($_SESSION['skill_errors'])) {
                echo '<div class="error-box">';
                foreach ($_SESSION['skill_errors'] as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
                unset($_SESSION['skill_errors']);
            }
            ?>

          
            <div class="add-skill-box">
                <h3>Add a Skill</h3>
                <form action="../../controllers/student_controller.php" method="POST" class="add-skill-form">
                    <input type="hidden" name="action" value="add_skill">

                    <input type="text" name="skillName" placeholder="e.g. Python, Graphic Design, Public Speaking" required>

                    <select name="proficiencyLevel">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>

                    <button type="submit">Add Skill</button>
                </form>
            </div>

           
            <h3 class="section-title">Your Skills (<?php echo count($mySkills); ?>)</h3>

            <?php if (count($mySkills) == 0) { ?>
                <p class="empty-text">You haven't added any skills yet.</p>
            <?php } else { ?>

                <div class="skill-tags">
                    <?php foreach ($mySkills as $skill) { ?>
                        <div class="skill-tag">
                            <span class="skill-name"><?php echo htmlspecialchars($skill['name']); ?></span>
                            <span class="skill-level"><?php echo htmlspecialchars($skill['proficiencyLevel']); ?></span>
                            <form action="../../controllers/student_controller.php" method="POST" class="delete-skill-form">
                                <input type="hidden" name="action" value="delete_skill">
                                <input type="hidden" name="profileSkillId" value="<?php echo $skill['profileSkillId']; ?>">
                                <button type="submit" class="delete-x" title="Remove skill">&times;</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>

        </div>

    </div>

</body>
</html>
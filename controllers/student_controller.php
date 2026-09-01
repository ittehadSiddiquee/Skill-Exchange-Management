<?php


require_once '../config/db_connect.php';
require_once '../models/student_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'student') {
    header("Location: ../views/auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {

    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $enrollmentNo = mysqli_real_escape_string($conn, $_POST['enrollmentNo']);
    $batch = mysqli_real_escape_string($conn, $_POST['batch']);
    $cgpa = mysqli_real_escape_string($conn, $_POST['cgpa']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin']);
    $profilePicture = mysqli_real_escape_string($conn, $_POST['profilePicture']);


    $errors = array();

    if (empty($fullName) || empty($department)) {
        $errors[] = "Full name and department cannot be empty.";
    }

    if (!empty($cgpa)) {
        if (!is_numeric($cgpa) || $cgpa < 0 || $cgpa > 4) {
            $errors[] = "CGPA must be a number between 0 and 4.";
        }
    }


    if (count($errors) > 0) {
        $_SESSION['profile_errors'] = $errors;
        header("Location: ../views/student/profile.php");
        exit();
    }

    
    update_user_info($conn, $userId, $fullName, $phone, $department);
    update_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture);
    update_student_info($conn, $userId, $enrollmentNo, $batch, $cgpa);

    
    $_SESSION['fullName'] = $fullName;

   
    $_SESSION['profile_success'] = "Profile updated successfully!";
    header("Location: ../views/student/profile.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_request') {

    $receiverId = mysqli_real_escape_string($conn, $_POST['receiverId']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $preferredTime = mysqli_real_escape_string($conn, $_POST['preferredTime']);

   
    $returnPage = isset($_POST['returnPage']) ? $_POST['returnPage'] : 'find_mentors.php';
 
    if ($returnPage != 'find_mentors.php' && $returnPage != 'find_alumni.php') {
        $returnPage = 'find_mentors.php';
    }

    $errors = array();

    if (empty($message)) {
        $errors[] = "Please write a short message explaining what you need help with.";
    }


    if (has_pending_request($conn, $userId, $receiverId)) {
        $errors[] = "You already have a pending request with this person.";
    }

    if (count($errors) > 0) {
        $_SESSION['request_errors'] = $errors;
        header("Location: ../views/student/" . $returnPage);
        exit();
    }

    send_skill_request($conn, $userId, $receiverId, 'skill', $message, $preferredTime);

    $_SESSION['request_success'] = "Your request has been sent!";
    header("Location: ../views/student/" . $returnPage);
    exit();
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_skill') {

    $skillName = trim($_POST['skillName']);
    $proficiencyLevel = $_POST['proficiencyLevel'];

    $errors = array();

    if (empty($skillName)) {
        $errors[] = "Please type a skill name.";
    }

    if (count($errors) > 0) {
        $_SESSION['skill_errors'] = $errors;
        header("Location: ../views/student/skills.php");
        exit();
    }

    $profileId = get_profile_id($conn, $userId);

 
    $skillId = find_skill_by_name($conn, $skillName);

    
    if ($skillId == null) {
        $skillId = create_skill($conn, $skillName);
    }



    if (skill_already_added($conn, $profileId, $skillId)) {
        $_SESSION['skill_errors'] = array("You've already added this skill to your profile.");
        header("Location: ../views/student/skills.php");
        exit();
    }

    add_skill_to_profile($conn, $profileId, $skillId, $proficiencyLevel);

    $_SESSION['skill_success'] = "Skill added successfully!";
    header("Location: ../views/student/skills.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_skill') {

    $profileSkillId = mysqli_real_escape_string($conn, $_POST['profileSkillId']);
    $profileId = get_profile_id($conn, $userId);

    delete_skill_from_profile($conn, $profileSkillId, $profileId);

    $_SESSION['skill_success'] = "Skill removed.";
    header("Location: ../views/student/skills.php");
    exit();
}
?>
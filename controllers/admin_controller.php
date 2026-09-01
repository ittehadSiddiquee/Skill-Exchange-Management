<?php


require_once '../config/db_connect.php';
require_once '../models/admin_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'admin') {
    header("Location: ../views/auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {

  
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $adminLevel = mysqli_real_escape_string($conn, $_POST['adminLevel']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin']);
    $profilePicture = mysqli_real_escape_string($conn, $_POST['profilePicture']);

   
    $errors = array();

    if (empty($fullName) || empty($department)) {
        $errors[] = "Full name and department cannot be empty.";
    }

   
    if (count($errors) > 0) {
        $_SESSION['profile_errors'] = $errors;
        header("Location: ../views/admin/profile.php");
        exit();
    }

    
    update_admin_user_info($conn, $userId, $fullName, $phone, $department);
    update_admin_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture);
    update_admin_info($conn, $userId, $adminLevel);

 
    $_SESSION['fullName'] = $fullName;

    $_SESSION['profile_success'] = "Profile updated successfully!";
    header("Location: ../views/admin/profile.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'approve_mentor') {
    $mentorId = mysqli_real_escape_string($conn, $_POST['mentorId']);
    approve_mentor($conn, $mentorId);

    $_SESSION['approval_success'] = "Mentor approved successfully.";
    header("Location: ../views/admin/approvals.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reject_mentor') {
    $mentorId = mysqli_real_escape_string($conn, $_POST['mentorId']);
    $targetUserId = mysqli_real_escape_string($conn, $_POST['userId']);
    reject_mentor($conn, $mentorId, $targetUserId);

    $_SESSION['approval_success'] = "Mentor application rejected and removed.";
    header("Location: ../views/admin/approvals.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'approve_alumni') {
    $alumniId = mysqli_real_escape_string($conn, $_POST['alumniId']);
    approve_alumni($conn, $alumniId);

    $_SESSION['approval_success'] = "Alumni approved successfully.";
    header("Location: ../views/admin/approvals.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reject_alumni') {
    $alumniId = mysqli_real_escape_string($conn, $_POST['alumniId']);
    $targetUserId = mysqli_real_escape_string($conn, $_POST['userId']);
    reject_alumni($conn, $alumniId, $targetUserId);

    $_SESSION['approval_success'] = "Alumni application rejected and removed.";
    header("Location: ../views/admin/approvals.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'toggle_active') {

    $targetUserId = mysqli_real_escape_string($conn, $_POST['userId']);
    $newStatus = mysqli_real_escape_string($conn, $_POST['newStatus']); // 1 or 0

    toggle_user_active($conn, $targetUserId, $newStatus);

    if ($newStatus == '1') {
        $_SESSION['users_success'] = "User account reactivated.";
    } else {
        $_SESSION['users_success'] = "User account deactivated.";
    }

    header("Location: ../views/admin/users.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_announcement') {

    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $targetRole = mysqli_real_escape_string($conn, $_POST['targetRole']);

    $errors = array();

    if (empty($message)) {
        $errors[] = "Please write a message before sending.";
    }

    if (count($errors) > 0) {
        $_SESSION['announcement_errors'] = $errors;
        header("Location: ../views/admin/announcements.php");
        exit();
    }

    $adminId = get_admin_id($conn, $userId);
    create_announcement($conn, $adminId, $message, $targetRole);

    $_SESSION['announcement_success'] = "Announcement sent successfully!";
    header("Location: ../views/admin/announcements.php");
    exit();
}
?>
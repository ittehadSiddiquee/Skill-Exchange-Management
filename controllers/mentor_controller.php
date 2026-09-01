<?php


require_once '../config/db_connect.php';
require_once '../models/mentor_model.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'mentor') {
    header("Location: ../views/auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {

   
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $expertise = mysqli_real_escape_string($conn, $_POST['expertise']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin']);
    $profilePicture = mysqli_real_escape_string($conn, $_POST['profilePicture']);

 
    $errors = array();

    if (empty($fullName) || empty($department)) {
        $errors[] = "Full name and department cannot be empty.";
    }

    if (empty($expertise)) {
        $errors[] = "Please list at least one area of expertise.";
    }

    if (count($errors) > 0) {
        $_SESSION['profile_errors'] = $errors;
        header("Location: ../views/mentor/profile.php");
        exit();
    }

 
    update_mentor_user_info($conn, $userId, $fullName, $phone, $department);
    update_mentor_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture);
    update_mentor_info($conn, $userId, $expertise, $experience, $availability);


    $_SESSION['fullName'] = $fullName;

  
    $_SESSION['profile_success'] = "Profile updated successfully!";
    header("Location: ../views/mentor/profile.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'accept_request') {
    $requestId = mysqli_real_escape_string($conn, $_POST['requestId']);
    accept_request($conn, $requestId);

    $_SESSION['request_action_success'] = "Request accepted.";
    header("Location: ../views/mentor/requests.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reject_request') {
    $requestId = mysqli_real_escape_string($conn, $_POST['requestId']);
    reject_request($conn, $requestId);

    $_SESSION['request_action_success'] = "Request rejected.";
    header("Location: ../views/mentor/requests.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_request') {

    $receiverId = mysqli_real_escape_string($conn, $_POST['receiverId']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $preferredTime = mysqli_real_escape_string($conn, $_POST['preferredTime']);

    $errors = array();

    if (empty($message)) {
        $errors[] = "Please write a short message explaining what you need help with.";
    }

    if (has_pending_request($conn, $userId, $receiverId)) {
        $errors[] = "You already have a pending request with this person.";
    }

    if (count($errors) > 0) {
        $_SESSION['request_errors'] = $errors;
        header("Location: ../views/mentor/find_alumni.php");
        exit();
    }

    send_skill_request($conn, $userId, $receiverId, 'collaboration', $message, $preferredTime);

    $_SESSION['request_success'] = "Your request has been sent!";
    header("Location: ../views/mentor/find_alumni.php");
    exit();
}
?>
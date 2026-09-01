<?php


require_once '../config/db_connect.php';
require_once '../models/alumni_model.php';


if (!isset($_SESSION['userId']) || $_SESSION['role'] != 'alumni') {
    header("Location: ../views/auth/login.php");
    exit();
}

$userId = $_SESSION['userId'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {

   
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $graduationYear = mysqli_real_escape_string($conn, $_POST['graduationYear']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $industry = mysqli_real_escape_string($conn, $_POST['industry']);
    $commPreference = mysqli_real_escape_string($conn, $_POST['commPreference']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin']);
    $profilePicture = mysqli_real_escape_string($conn, $_POST['profilePicture']);

 
    $errors = array();

    if (empty($fullName) || empty($department)) {
        $errors[] = "Full name and department cannot be empty.";
    }

    if (!empty($graduationYear)) {
        if (!is_numeric($graduationYear) || strlen($graduationYear) != 4) {
            $errors[] = "Graduation year must be a valid 4-digit year (e.g. 2022).";
        }
    }

  
    if (count($errors) > 0) {
        $_SESSION['profile_errors'] = $errors;
        header("Location: ../views/alumni/profile.php");
        exit();
    }

    //
    update_alumni_user_info($conn, $userId, $fullName, $phone, $department);
    update_alumni_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture);
    update_alumni_info($conn, $userId, $graduationYear, $company, $industry, $commPreference);

    
    $_SESSION['fullName'] = $fullName;

 
    $_SESSION['profile_success'] = "Profile updated successfully!";
    header("Location: ../views/alumni/profile.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_post') {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);

    $errors = array();

    if (empty($title) || empty($content)) {
        $errors[] = "Please fill in both the title and content.";
    }

    if (count($errors) > 0) {
        $_SESSION['post_errors'] = $errors;
        header("Location: ../views/alumni/posts.php");
        exit();
    }

    $alumniId = get_alumni_id($conn, $userId);
    create_post($conn, $alumniId, $title, $content, $link);

    $_SESSION['post_success'] = "Post published successfully!";
    header("Location: ../views/alumni/posts.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_post') {

    $postId = mysqli_real_escape_string($conn, $_POST['postId']);
    $alumniId = get_alumni_id($conn, $userId);

    delete_post($conn, $postId, $alumniId);

    $_SESSION['post_success'] = "Post deleted.";
    header("Location: ../views/alumni/posts.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'accept_request') {
    $requestId = mysqli_real_escape_string($conn, $_POST['requestId']);
    accept_request($conn, $requestId);

    $_SESSION['request_action_success'] = "Request accepted.";
    header("Location: ../views/alumni/requests.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reject_request') {
    $requestId = mysqli_real_escape_string($conn, $_POST['requestId']);
    reject_request($conn, $requestId);

    $_SESSION['request_action_success'] = "Request rejected.";
    header("Location: ../views/alumni/requests.php");
    exit();
}
?>
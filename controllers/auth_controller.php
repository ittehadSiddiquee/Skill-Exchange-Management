<?php


require_once '../config/db_connect.php';
require_once '../models/user_model.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {

    
    $role = $_POST['role'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

   
    $errors = array();

    if (empty($fullName) || empty($email) || empty($password) || empty($department)) {
        $errors[] = "Please fill in all required fields.";
    }

    if ($password != $confirmPassword) {
        $errors[] = "Password and Confirm Password do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

   
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }


    if (email_already_exists($conn, $email)) {
        $errors[] = "This email is already registered. Please login instead.";
    }

    if (count($errors) > 0) {
        $_SESSION['register_errors'] = $errors;
        header("Location: ../views/auth/register.php");
        exit();
    }


    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

   
    $fullName = mysqli_real_escape_string($conn, $fullName);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $department = mysqli_real_escape_string($conn, $department);
    $role = mysqli_real_escape_string($conn, $role);


    $newUserId = insert_user($conn, $fullName, $email, $passwordHash, $phone, $department, $role);

    if ($role == 'student') {
        $enrollmentNo = mysqli_real_escape_string($conn, $_POST['enrollmentNo']);
        $batch = mysqli_real_escape_string($conn, $_POST['batch']);
        insert_student($conn, $newUserId, $enrollmentNo, $batch);

    } elseif ($role == 'mentor') {
        $expertise = mysqli_real_escape_string($conn, $_POST['expertise']);
        $experience = mysqli_real_escape_string($conn, $_POST['experience']);
        insert_mentor($conn, $newUserId, $expertise, $experience);

    } elseif ($role == 'alumni') {
        $graduationYear = mysqli_real_escape_string($conn, $_POST['graduationYear']);
        $company = mysqli_real_escape_string($conn, $_POST['company']);
        $industry = mysqli_real_escape_string($conn, $_POST['industry']);
        insert_alumni($conn, $newUserId, $graduationYear, $company, $industry);
    }

    $sql = "INSERT INTO profile (userId, bio, interests) VALUES ('$newUserId', '', '')";
    mysqli_query($conn, $sql);

    $_SESSION['register_success'] = "Account created successfully! Please login.";
    header("Location: ../views/auth/login.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 
    $errors = array();

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    }

    $user = get_user_by_email($conn, $email);

    if ($user == null) {
        $errors[] = "No account found with that email.";
    } else {
        
        if (!password_verify($password, $user['passwordHash'])) {
            $errors[] = "Incorrect password. Please try again.";
        }

   
        if ($user['isActive'] == 0) {
            $errors[] = "This account has been deactivated. Please contact an administrator.";
        }

        
        if (!is_user_approved($conn, $user['userId'], $user['role'])) {
            $errors[] = "Your account is still pending admin approval. Please check back later.";
        }
    }


    if (count($errors) > 0) {
        $_SESSION['login_errors'] = $errors;
        header("Location: ../views/auth/login.php");
        exit();
    }

    $_SESSION['userId'] = $user['userId'];
    $_SESSION['fullName'] = $user['fullName'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

   
    if ($user['role'] == 'student') {
        header("Location: ../views/student/dashboard.php");
    } elseif ($user['role'] == 'mentor') {
        header("Location: ../views/mentor/dashboard.php");
    } elseif ($user['role'] == 'alumni') {
        header("Location: ../views/alumni/dashboard.php");
    } elseif ($user['role'] == 'admin') {
        header("Location: ../views/admin/dashboard.php");
    }
    exit();
}
?>
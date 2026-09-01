<?php



function email_already_exists($conn, $email) {
    $sql = "SELECT userId FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}




function insert_user($conn, $fullName, $email, $passwordHash, $phone, $department, $role) {
    $sql = "INSERT INTO users (fullName, email, passwordHash, phone, department, role, isActive)
            VALUES ('$fullName', '$email', '$passwordHash', '$phone', '$department', '$role', 1)";

    mysqli_query($conn, $sql);

    

    $newUserId = mysqli_insert_id($conn);
    return $newUserId;
}



function insert_student($conn, $userId, $enrollmentNo, $batch) {
    $sql = "INSERT INTO student (userId, enrollmentNo, batch)
            VALUES ('$userId', '$enrollmentNo', '$batch')";
    mysqli_query($conn, $sql);
}




function insert_mentor($conn, $userId, $expertise, $experience) {
    $sql = "INSERT INTO mentor (userId, expertise, experience, isApproved)
            VALUES ('$userId', '$expertise', '$experience', 0)";
    mysqli_query($conn, $sql);
}




function insert_alumni($conn, $userId, $graduationYear, $company, $industry) {
    $sql = "INSERT INTO alumni (userId, graduationYear, company, industry, isApproved)
            VALUES ('$userId', '$graduationYear', '$company', '$industry', 0)";
    mysqli_query($conn, $sql);
}






function get_user_by_email($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); 
    } else {
        return null; 
    }
}






function is_user_approved($conn, $userId, $role) {

    if ($role == 'mentor') {
        $sql = "SELECT isApproved FROM mentor WHERE userId = '$userId'";
    } elseif ($role == 'alumni') {
        $sql = "SELECT isApproved FROM alumni WHERE userId = '$userId'";
    } else {

        return true;
    }

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['isApproved'] == 1) {
        return true;
    } else {
        return false;
    }
}
?>
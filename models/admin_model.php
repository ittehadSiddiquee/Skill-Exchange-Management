<?php

function get_admin_full_profile($conn, $userId) {

    $data = array();

    
    $sql1 = "SELECT * FROM users WHERE userId = '$userId'";
    $result1 = mysqli_query($conn, $sql1);
    $userRow = mysqli_fetch_assoc($result1);

   
    $sql2 = "SELECT * FROM profile WHERE userId = '$userId'";
    $result2 = mysqli_query($conn, $sql2);
    $profileRow = mysqli_fetch_assoc($result2);

    
    $sql3 = "SELECT * FROM admin WHERE userId = '$userId'";
    $result3 = mysqli_query($conn, $sql3);
    $adminRow = mysqli_fetch_assoc($result3);

    if ($userRow) { $data = array_merge($data, $userRow); }
    if ($profileRow) { $data = array_merge($data, $profileRow); }
    if ($adminRow) { $data = array_merge($data, $adminRow); }

    return $data;
}


function update_admin_user_info($conn, $userId, $fullName, $phone, $department) {
    $sql = "UPDATE users
            SET fullName = '$fullName', phone = '$phone', department = '$department'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function update_admin_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture) {
    $sql = "UPDATE profile
            SET bio = '$bio', interests = '$interests', linkedin = '$linkedin', profilePicture = '$profilePicture'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function update_admin_info($conn, $userId, $adminLevel) {
    $sql = "UPDATE admin
            SET adminLevel = '$adminLevel'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function count_total_users($conn) {
    $sql = "SELECT COUNT(*) AS total FROM users";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}


function count_pending_mentors($conn) {
    $sql = "SELECT COUNT(*) AS total FROM mentor WHERE isApproved = 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}


function count_pending_alumni($conn) {
    $sql = "SELECT COUNT(*) AS total FROM alumni WHERE isApproved = 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}


function get_pending_mentors($conn) {
    $sql = "SELECT mentor.mentorId, mentor.userId, mentor.expertise, mentor.experience,
                   users.fullName, users.email, users.department
            FROM mentor
            INNER JOIN users ON mentor.userId = users.userId
            WHERE mentor.isApproved = 0";
    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}



function get_pending_alumni($conn) {
    $sql = "SELECT alumni.alumniId, alumni.userId, alumni.graduationYear, alumni.company,
                   users.fullName, users.email, users.department
            FROM alumni
            INNER JOIN users ON alumni.userId = users.userId
            WHERE alumni.isApproved = 0";
    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}




function approve_mentor($conn, $mentorId) {
    $sql = "UPDATE mentor SET isApproved = 1 WHERE mentorId = '$mentorId'";
    mysqli_query($conn, $sql);
}


function approve_alumni($conn, $alumniId) {
    $sql = "UPDATE alumni SET isApproved = 1 WHERE alumniId = '$alumniId'";
    mysqli_query($conn, $sql);
}


function reject_mentor($conn, $mentorId, $userId) {
    $sql1 = "DELETE FROM mentor WHERE mentorId = '$mentorId'";
    mysqli_query($conn, $sql1);

    $sql2 = "DELETE FROM profile WHERE userId = '$userId'";
    mysqli_query($conn, $sql2);

    $sql3 = "DELETE FROM users WHERE userId = '$userId'";
    mysqli_query($conn, $sql3);
}


function reject_alumni($conn, $alumniId, $userId) {
    $sql1 = "DELETE FROM alumni WHERE alumniId = '$alumniId'";
    mysqli_query($conn, $sql1);

    $sql2 = "DELETE FROM profile WHERE userId = '$userId'";
    mysqli_query($conn, $sql2);

    $sql3 = "DELETE FROM users WHERE userId = '$userId'";
    mysqli_query($conn, $sql3);
}




function get_all_users($conn, $roleFilter, $keyword) {

    $sql = "SELECT userId, fullName, email, phone, department, role, isActive, createdAt FROM users WHERE 1=1";

 
    if (!empty($roleFilter) && $roleFilter != 'all') {
        $roleFilter = mysqli_real_escape_string($conn, $roleFilter);
        $sql .= " AND role = '$roleFilter'";
    }

  
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($conn, $keyword);
        $sql .= " AND (fullName LIKE '%$keyword%' OR email LIKE '%$keyword%')";
    }

    $sql .= " ORDER BY createdAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}



function toggle_user_active($conn, $userId, $newStatus) {
    $newStatus = mysqli_real_escape_string($conn, $newStatus);
    $sql = "UPDATE users SET isActive = '$newStatus' WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}




function get_admin_id($conn, $userId) {
    $sql = "SELECT adminId FROM admin WHERE userId = '$userId'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['adminId'];
}


function create_announcement($conn, $adminId, $message, $targetRole) {
    $sql = "INSERT INTO announcement (adminId, message, targetRole)
            VALUES ('$adminId', '$message', '$targetRole')";
    mysqli_query($conn, $sql);
}




function get_all_announcements($conn) {
    $sql = "SELECT announcement.announcementId, announcement.message, announcement.targetRole, announcement.sentAt,
                   users.fullName AS sentBy
            FROM announcement
            INNER JOIN admin ON announcement.adminId = admin.adminId
            INNER JOIN users ON admin.userId = users.userId
            ORDER BY announcement.sentAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}
?>
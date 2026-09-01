<?php

function get_mentor_full_profile($conn, $userId) {

    $data = array();

    
    $sql1 = "SELECT * FROM users WHERE userId = '$userId'";
    $result1 = mysqli_query($conn, $sql1);
    $userRow = mysqli_fetch_assoc($result1);

    
    $sql2 = "SELECT * FROM profile WHERE userId = '$userId'";
    $result2 = mysqli_query($conn, $sql2);
    $profileRow = mysqli_fetch_assoc($result2);

    
    $sql3 = "SELECT * FROM mentor WHERE userId = '$userId'";
    $result3 = mysqli_query($conn, $sql3);
    $mentorRow = mysqli_fetch_assoc($result3);

    if ($userRow) { $data = array_merge($data, $userRow); }
    if ($profileRow) { $data = array_merge($data, $profileRow); }
    if ($mentorRow) { $data = array_merge($data, $mentorRow); }

    return $data;
}


function update_mentor_user_info($conn, $userId, $fullName, $phone, $department) {
    $sql = "UPDATE users
            SET fullName = '$fullName', phone = '$phone', department = '$department'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function update_mentor_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture) {
    $sql = "UPDATE profile
            SET bio = '$bio', interests = '$interests', linkedin = '$linkedin', profilePicture = '$profilePicture'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function update_mentor_info($conn, $userId, $expertise, $experience, $availability) {
    $sql = "UPDATE mentor
            SET expertise = '$expertise', experience = '$experience', availability = '$availability'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function get_pending_requests_for_mentor($conn, $mentorUserId) {
    $sql = "SELECT skill_request.requestId, skill_request.message, skill_request.preferredTime,
                   skill_request.status, skill_request.createdAt,
                   users.fullName, users.email, users.department
            FROM skill_request
            INNER JOIN users ON skill_request.senderId = users.userId
            WHERE skill_request.receiverId = '$mentorUserId'
            AND skill_request.status = 'pending'
            ORDER BY skill_request.createdAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}


function get_responded_requests_for_mentor($conn, $mentorUserId) {
    $sql = "SELECT skill_request.requestId, skill_request.message, skill_request.status, skill_request.createdAt,
                   users.fullName
            FROM skill_request
            INNER JOIN users ON skill_request.senderId = users.userId
            WHERE skill_request.receiverId = '$mentorUserId'
            AND skill_request.status != 'pending'
            ORDER BY skill_request.createdAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}


function accept_request($conn, $requestId) {
    $sql = "UPDATE skill_request SET status = 'accepted' WHERE requestId = '$requestId'";
    mysqli_query($conn, $sql);
}


function reject_request($conn, $requestId) {
    $sql = "UPDATE skill_request SET status = 'rejected' WHERE requestId = '$requestId'";
    mysqli_query($conn, $sql);
}


function get_announcements_for_role($conn, $role) {
    $role = mysqli_real_escape_string($conn, $role);

    $sql = "SELECT message, targetRole, sentAt
            FROM announcement
            WHERE targetRole = 'all' OR targetRole = '$role'
            ORDER BY sentAt DESC
            LIMIT 5";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}


function search_alumni($conn, $keyword) {
    $keyword = mysqli_real_escape_string($conn, $keyword);

    $sql = "SELECT alumni.alumniId, alumni.userId, alumni.graduationYear, alumni.company, alumni.industry,
                   users.fullName, users.department,
                   profile.bio, profile.linkedin
            FROM alumni
            INNER JOIN users ON alumni.userId = users.userId
            LEFT JOIN profile ON alumni.userId = profile.userId
            WHERE alumni.isApproved = 1
            AND (
                users.fullName LIKE '%$keyword%'
                OR users.department LIKE '%$keyword%'
                OR alumni.company LIKE '%$keyword%'
                OR alumni.industry LIKE '%$keyword%'
            )";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}




function get_all_approved_alumni($conn) {
    $sql = "SELECT alumni.alumniId, alumni.userId, alumni.graduationYear, alumni.company, alumni.industry,
                   users.fullName, users.department,
                   profile.bio, profile.linkedin
            FROM alumni
            INNER JOIN users ON alumni.userId = users.userId
            LEFT JOIN profile ON alumni.userId = profile.userId
            WHERE alumni.isApproved = 1";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}




function get_posts_by_alumni($conn, $alumniId) {
    $sql = "SELECT title, content, link, createdAt
            FROM alumni_post
            WHERE alumniId = '$alumniId'
            ORDER BY createdAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}



function send_skill_request($conn, $senderId, $receiverId, $type, $message, $preferredTime) {
    $sql = "INSERT INTO skill_request (senderId, receiverId, type, message, preferredTime, status)
            VALUES ('$senderId', '$receiverId', '$type', '$message', '$preferredTime', 'pending')";
    mysqli_query($conn, $sql);
}




function has_pending_request($conn, $senderId, $receiverId) {
    $sql = "SELECT requestId FROM skill_request
            WHERE senderId = '$senderId' AND receiverId = '$receiverId' AND status = 'pending'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}
?>
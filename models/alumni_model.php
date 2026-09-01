<?php

function get_alumni_full_profile($conn, $userId) {

    $data = array();

   
    $sql1 = "SELECT * FROM users WHERE userId = '$userId'";
    $result1 = mysqli_query($conn, $sql1);
    $userRow = mysqli_fetch_assoc($result1);

 
    $sql2 = "SELECT * FROM profile WHERE userId = '$userId'";
    $result2 = mysqli_query($conn, $sql2);
    $profileRow = mysqli_fetch_assoc($result2);

   
    $sql3 = "SELECT * FROM alumni WHERE userId = '$userId'";
    $result3 = mysqli_query($conn, $sql3);
    $alumniRow = mysqli_fetch_assoc($result3);

    if ($userRow) { $data = array_merge($data, $userRow); }
    if ($profileRow) { $data = array_merge($data, $profileRow); }
    if ($alumniRow) { $data = array_merge($data, $alumniRow); }

    return $data;
}


function update_alumni_user_info($conn, $userId, $fullName, $phone, $department) {
    $sql = "UPDATE users
            SET fullName = '$fullName', phone = '$phone', department = '$department'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}





function update_alumni_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture) {
    $sql = "UPDATE profile
            SET bio = '$bio', interests = '$interests', linkedin = '$linkedin', profilePicture = '$profilePicture'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}




function update_alumni_info($conn, $userId, $graduationYear, $company, $industry, $commPreference) {
    $sql = "UPDATE alumni
            SET graduationYear = '$graduationYear', company = '$company', industry = '$industry', commPreference = '$commPreference'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function get_alumni_id($conn, $userId) {
    $sql = "SELECT alumniId FROM alumni WHERE userId = '$userId'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['alumniId'];
}


function create_post($conn, $alumniId, $title, $content, $link) {
    $sql = "INSERT INTO alumni_post (alumniId, title, content, link)
            VALUES ('$alumniId', '$title', '$content', '$link')";
    mysqli_query($conn, $sql);
}




function get_my_posts($conn, $alumniId) {
    $sql = "SELECT * FROM alumni_post WHERE alumniId = '$alumniId' ORDER BY createdAt DESC";
    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}



function delete_post($conn, $postId, $alumniId) {
    $sql = "DELETE FROM alumni_post WHERE postId = '$postId' AND alumniId = '$alumniId'";
    mysqli_query($conn, $sql);
}


function get_all_posts($conn) {
    $sql = "SELECT alumni_post.postId, alumni_post.title, alumni_post.content,
                   alumni_post.link, alumni_post.createdAt,
                   users.fullName
            FROM alumni_post
            INNER JOIN alumni ON alumni_post.alumniId = alumni.alumniId
            INNER JOIN users ON alumni.userId = users.userId
            ORDER BY alumni_post.createdAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
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


function get_pending_requests_for_alumni($conn, $alumniUserId) {
    $sql = "SELECT skill_request.requestId, skill_request.message, skill_request.preferredTime,
                   skill_request.status, skill_request.createdAt,
                   users.fullName, users.email, users.department, users.role
            FROM skill_request
            INNER JOIN users ON skill_request.senderId = users.userId
            WHERE skill_request.receiverId = '$alumniUserId'
            AND skill_request.status = 'pending'
            ORDER BY skill_request.createdAt DESC";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}


function get_responded_requests_for_alumni($conn, $alumniUserId) {
    $sql = "SELECT skill_request.requestId, skill_request.message, skill_request.status, skill_request.createdAt,
                   users.fullName, users.role
            FROM skill_request
            INNER JOIN users ON skill_request.senderId = users.userId
            WHERE skill_request.receiverId = '$alumniUserId'
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
?>
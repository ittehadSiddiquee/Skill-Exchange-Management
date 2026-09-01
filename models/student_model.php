<?php

function get_student_full_profile($conn, $userId) {

    $data = array(); 


    
    $sql1 = "SELECT * FROM users WHERE userId = '$userId'";
    $result1 = mysqli_query($conn, $sql1);
    $userRow = mysqli_fetch_assoc($result1);




    $sql2 = "SELECT * FROM profile WHERE userId = '$userId'";
    $result2 = mysqli_query($conn, $sql2);
    $profileRow = mysqli_fetch_assoc($result2);

 
    $sql3 = "SELECT * FROM student WHERE userId = '$userId'";
    $result3 = mysqli_query($conn, $sql3);
    $studentRow = mysqli_fetch_assoc($result3);

 
    if ($userRow) { $data = array_merge($data, $userRow); }
    if ($profileRow) { $data = array_merge($data, $profileRow); }
    if ($studentRow) { $data = array_merge($data, $studentRow); }

    return $data;
}


function update_user_info($conn, $userId, $fullName, $phone, $department) {
    $sql = "UPDATE users
            SET fullName = '$fullName', phone = '$phone', department = '$department'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function update_profile_info($conn, $userId, $bio, $interests, $linkedin, $profilePicture) {
    $sql = "UPDATE profile
            SET bio = '$bio', interests = '$interests', linkedin = '$linkedin', profilePicture = '$profilePicture'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function update_student_info($conn, $userId, $enrollmentNo, $batch, $cgpa) {
    $sql = "UPDATE student
            SET enrollmentNo = '$enrollmentNo', batch = '$batch', cgpa = '$cgpa'
            WHERE userId = '$userId'";
    mysqli_query($conn, $sql);
}


function search_mentors($conn, $keyword) {

    $keyword = mysqli_real_escape_string($conn, $keyword);

    $sql = "SELECT mentor.mentorId, mentor.userId, mentor.expertise, mentor.experience, mentor.availability,
                   users.fullName, users.department,
                   profile.bio
            FROM mentor
            INNER JOIN users ON mentor.userId = users.userId
            LEFT JOIN profile ON mentor.userId = profile.userId
            WHERE mentor.isApproved = 1
            AND (
                users.fullName LIKE '%$keyword%'
                OR mentor.expertise LIKE '%$keyword%'
                OR users.department LIKE '%$keyword%'
            )";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}




function get_all_approved_mentors($conn) {
    $sql = "SELECT mentor.mentorId, mentor.userId, mentor.expertise, mentor.experience, mentor.availability,
                   users.fullName, users.department,
                   profile.bio
            FROM mentor
            INNER JOIN users ON mentor.userId = users.userId
            LEFT JOIN profile ON mentor.userId = profile.userId
            WHERE mentor.isApproved = 1";

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






function get_profile_id($conn, $userId) {
    $sql = "SELECT profileId FROM profile WHERE userId = '$userId'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['profileId'];
}





function find_skill_by_name($conn, $name) {
    $name = mysqli_real_escape_string($conn, $name);
    $sql = "SELECT skillId FROM skill WHERE LOWER(name) = LOWER('$name')";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['skillId'];
    } else {
        return null;
    }
}






function create_skill($conn, $name) {
    $name = mysqli_real_escape_string($conn, $name);
    $sql = "INSERT INTO skill (name) VALUES ('$name')";
    mysqli_query($conn, $sql);
    return mysqli_insert_id($conn);
}




function skill_already_added($conn, $profileId, $skillId) {
    $sql = "SELECT profileSkillId FROM profile_skill WHERE profileId = '$profileId' AND skillId = '$skillId'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}




function add_skill_to_profile($conn, $profileId, $skillId, $proficiencyLevel) {
    $sql = "INSERT INTO profile_skill (profileId, skillId, proficiencyLevel)
            VALUES ('$profileId', '$skillId', '$proficiencyLevel')";
    mysqli_query($conn, $sql);
}




function get_my_skills($conn, $profileId) {
    $sql = "SELECT profile_skill.profileSkillId, profile_skill.proficiencyLevel,
                   skill.skillId, skill.name
            FROM profile_skill
            INNER JOIN skill ON profile_skill.skillId = skill.skillId
            WHERE profile_skill.profileId = '$profileId'";

    $result = mysqli_query($conn, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}








function delete_skill_from_profile($conn, $profileSkillId, $profileId) {
    $sql = "DELETE FROM profile_skill WHERE profileSkillId = '$profileSkillId' AND profileId = '$profileId'";
    mysqli_query($conn, $sql);
}







function get_my_sent_requests($conn, $studentUserId) {
    $sql = "SELECT skill_request.requestId, skill_request.message, skill_request.preferredTime,
                   skill_request.status, skill_request.createdAt,
                   users.fullName AS mentorName, users.department AS mentorDepartment
            FROM skill_request
            INNER JOIN users ON skill_request.receiverId = users.userId
            WHERE skill_request.senderId = '$studentUserId'
            ORDER BY skill_request.createdAt DESC";

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
?>
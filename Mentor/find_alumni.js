function togglePosts(alumniId) {
    var sectionId = "posts-" + alumniId;
    var section = document.getElementById(sectionId);

    if (section.style.display === "none") {
        section.style.display = "block";
    } else {
        section.style.display = "none";
    }
}


function toggleRequestForm(userId) {
    var formId = "request-form-" + userId;
    var form = document.getElementById(formId);

    if (form.style.display === "none") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
function toggleRequestForm(mentorUserId) {
    var formId = "request-form-" + mentorUserId;
    var form = document.getElementById(formId);

    if (form.style.display === "none") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
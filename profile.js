

var profileForm = document.querySelector(".profile-form");
var fullNameInput = document.querySelector('input[name="fullName"]');

profileForm.addEventListener("submit", function (event) {
    if (fullNameInput.value.trim() === "") {
        event.preventDefault();
        alert("Full name cannot be empty.");
    }
});
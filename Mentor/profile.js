var profileForm = document.querySelector(".profile-form");
var expertiseInput = document.querySelector('input[name="expertise"]');

profileForm.addEventListener("submit", function (event) {
    if (expertiseInput.value.trim() === "") {
        event.preventDefault();
        alert("Please enter at least one area of expertise.");
    }
});
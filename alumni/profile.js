

var profileForm = document.querySelector(".profile-form");
var gradYearInput = document.getElementById("gradyear_input");

profileForm.addEventListener("submit", function (event) {
    var yearValue = gradYearInput.value.trim();

    if (yearValue !== "") {
        var yearNumber = parseInt(yearValue);

        if (isNaN(yearNumber) || yearValue.length != 4) {
            event.preventDefault();
            alert("Please enter a valid 4-digit graduation year, e.g. 2022.");
        }
    }
});
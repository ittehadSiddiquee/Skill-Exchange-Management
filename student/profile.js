

var profileForm = document.querySelector(".profile-form");
var cgpaInput = document.getElementById("cgpa_input");

profileForm.addEventListener("submit", function (event) {
    var cgpaValue = cgpaInput.value.trim();

    
    if (cgpaValue !== "") {
        var cgpaNumber = parseFloat(cgpaValue);

        if (isNaN(cgpaNumber) || cgpaNumber < 0 || cgpaNumber > 4) {
            event.preventDefault(); 
            alert("CGPA must be a number between 0 and 4.");
        }
    }
});
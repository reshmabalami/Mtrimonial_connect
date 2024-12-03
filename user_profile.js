
// Function to calculate age based on date of birth
function calculateAge() {
    var dob = document.getElementById("dob").value;
    var birthDate = new Date(dob);
    var age = new Date().getFullYear() - birthDate.getFullYear();
    var m = new Date().getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && new Date().getDate() < birthDate.getDate())) {
        age--;
    }
    document.getElementById("age").value = age;  // Set the age field
}

// Function to show the preview of the selected profile image
document.getElementById("profilePhoto").addEventListener("change", function(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var previewImage = document.getElementById("profilePicture");
        previewImage.src = reader.result;  // Display the selected image
    };
    reader.readAsDataURL(event.target.files[0]);
});

// Optional: You can add other JavaScript functions here as needed, such as form validation, dynamic field updates, etc.

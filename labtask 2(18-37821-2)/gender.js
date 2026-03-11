function validateGender() {
    // get the selected radio button
    let selected = document.querySelector('input[name="gender"]:checked');
    let error = document.getElementById("genderError");

    if(!selected){
        error.innerHTML = "Please select a gender";
        return false;
    }

    error.innerHTML = "";

    // Save the selected gender in localStorage
    localStorage.setItem("gender", selected.value);

    // Redirect to next page
    window.location.href = "dob.html";

    return false; // prevent default form submit
}
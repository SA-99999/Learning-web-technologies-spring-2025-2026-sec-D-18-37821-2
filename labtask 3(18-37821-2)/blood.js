function validateBloodGroup() {
    let blood = document.getElementById("blood").value;
    let error = document.getElementById("bloodError");

    // Check if a blood group is selected
    if(blood === ""){
        error.innerHTML = "Please select a blood group";
        return false; // stop form submission
    }

    // Clear any previous error
    error.innerHTML = "";

    // Save the selected blood group for later use
    localStorage.setItem("blood", blood);

    // Redirect to the next page (profile picture page)
    window.location.href = "pic.html";

    return false; // prevent page from refreshing
}
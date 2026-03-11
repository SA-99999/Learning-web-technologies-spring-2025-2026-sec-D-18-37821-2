function validateProfilePicture() {
    let userid = document.getElementById("userid").value.trim();
    let picture = document.getElementById("picture").value;
    let error = document.getElementById("pictureError");

    if(userid === ""){
        error.innerHTML = "User ID cannot be empty";
        return false;
    }

    if(isNaN(userid) || Number(userid) <= 0){
        error.innerHTML = "User ID must be a positive number";
        return false;
    }

    if(picture === ""){
        error.innerHTML = "Please select a picture";
        return false;
    }

    error.innerHTML = "";

    // Save info to localStorage
    localStorage.setItem("userid", userid);
    localStorage.setItem("picture", picture);

    // Redirect to final profile page
    window.location.href = "Profile.html";

    return false; // prevent default form submit
}
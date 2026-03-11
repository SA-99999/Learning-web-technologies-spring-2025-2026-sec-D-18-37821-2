function validateEmail() {
    let email = document.getElementById("email").value.trim();
    let error = document.getElementById("emailError");

    if(email === ""){
        error.innerHTML = "Email cannot be empty";
        return false;
    }

    let pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!pattern.test(email)){
        error.innerHTML = "Invalid email format";
        return false;
    }

    error.innerHTML = "";

    // Save email
    localStorage.setItem("email", email);

    // Redirect
    window.location.href = "gender.html";
    return false;
}
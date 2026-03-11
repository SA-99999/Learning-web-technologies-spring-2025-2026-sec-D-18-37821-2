function validateDOB() {
    let dd = document.getElementById("dd").value.trim();
    let mm = document.getElementById("mm").value.trim();
    let yyyy = document.getElementById("yyyy").value.trim();
    let error = document.getElementById("dobError");

    if(dd === "" || mm === "" || yyyy === ""){
        error.innerHTML = "All fields are required";
        return false;
    }

    if(isNaN(dd) || isNaN(mm) || isNaN(yyyy)){
        error.innerHTML = "DOB must be numeric";
        return false;
    }

    dd = parseInt(dd);
    mm = parseInt(mm);
    yyyy = parseInt(yyyy);

    if(dd < 0 || dd > 31 || mm < 1 || mm > 12 || yyyy < 1900 || yyyy > 2016){
        error.innerHTML = "DOB out of range";
        return false;
    }

    error.innerHTML = "";

    // Save DOB
    localStorage.setItem("dob", dd + "/" + mm + "/" + yyyy);

    // Redirect
    window.location.href = "degree.html";
    return false;
}
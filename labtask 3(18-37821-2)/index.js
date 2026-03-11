function validateName() {
    let name = document.getElementById("name").value.trim();
    let error = document.getElementById("nameError");

    if(name === ""){
        error.innerHTML = "Name cannot be empty";
        return false;
    }

    let words = name.split(" ").filter(w => w !== "");
    if(words.length < 2){
        error.innerHTML = "Name must have at least 2 words";
        return false;
    }

    if(!/[a-zA-Z]/.test(name[0])){
        error.innerHTML = "Name must start with a letter";
        return false;
    }

    if(!/^[a-zA-Z.\-\s]+$/.test(name)){
        error.innerHTML = "Only letters, dot, dash allowed";
        return false;
    }

    error.innerHTML = "";

    // Save name to localStorage
    localStorage.setItem("name", name);

    // Redirect to next page
    window.location.href = "email.html";

    return false; // prevent form refresh
}
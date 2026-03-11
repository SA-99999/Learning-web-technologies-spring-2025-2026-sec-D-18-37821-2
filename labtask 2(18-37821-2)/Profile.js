// Pre-fill form from localStorage
window.onload = function() {
    document.getElementById("name").value = localStorage.getItem("name") || "";
    document.getElementById("email").value = localStorage.getItem("email") || "";

    let gender = localStorage.getItem("gender") || "";
    if(gender === "Male") document.getElementById("male").checked = true;
    if(gender === "Female") document.getElementById("female").checked = true;
    if(gender === "Other") document.getElementById("other").checked = true;

    let dob = (localStorage.getItem("dob") || " / / ").split("/");
    document.getElementById("dd").value = dob[0] || "";
    document.getElementById("mm").value = dob[1] || "";
    document.getElementById("yyyy").value = dob[2] || "";

    let degree = (localStorage.getItem("degree") || "").split(", ");
    if(degree.includes("SSC")) document.getElementById("ssc").checked = true;
    if(degree.includes("HSC")) document.getElementById("hsc").checked = true;
    if(degree.includes("BSc")) document.getElementById("bsc").checked = true;
    if(degree.includes("MSc")) document.getElementById("msc").checked = true;

    document.getElementById("blood").value = localStorage.getItem("blood") || "";
    document.getElementById("userid").value = localStorage.getItem("userid") || "";
    // Cannot prefill file input for security
};

// On submit, just show alert with all info
function submitProfile() {
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let gender = document.querySelector('input[name="gender"]:checked')?.value || "";
    let dd = document.getElementById("dd").value;
    let mm = document.getElementById("mm").value;
    let yyyy = document.getElementById("yyyy").value;

    let degrees = [];
    if(document.getElementById("ssc").checked) degrees.push("SSC");
    if(document.getElementById("hsc").checked) degrees.push("HSC");
    if(document.getElementById("bsc").checked) degrees.push("BSc");
    if(document.getElementById("msc").checked) degrees.push("MSc");

    let blood = document.getElementById("blood").value;
    let userid = document.getElementById("userid").value;
    let picture = document.getElementById("picture").value;

    alert(
        "Final Profile Submitted!\n\n" +
        "Name: " + name + "\n" +
        "Email: " + email + "\n" +
        "Gender: " + gender + "\n" +
        "DOB: " + dd + "/" + mm + "/" + yyyy + "\n" +
        "Degree: " + degrees.join(", ") + "\n" +
        "Blood Group: " + blood + "\n" +
        "User ID: " + userid + "\n" +
        "Picture: " + picture
    );

    return false; // prevent default submit
}
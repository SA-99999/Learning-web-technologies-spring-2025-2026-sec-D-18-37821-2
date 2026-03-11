function validateDegree() {
    let ssc = document.getElementById("ssc").checked;
    let hsc = document.getElementById("hsc").checked;
    let bsc = document.getElementById("bsc").checked;
    let error = document.getElementById("degreeError");

    if(!ssc && !hsc && !bsc){
        error.innerHTML = "Select at least one degree";
        return false;
    }

    let degrees = [];
    if(ssc) degrees.push("SSC");
    if(hsc) degrees.push("HSC");
    if(bsc) degrees.push("BSc");

    error.innerHTML = "";

    // Save degrees
    localStorage.setItem("degree", degrees.join(", "));

    // Redirect
    window.location.href = "blood.html";
    return false;
}
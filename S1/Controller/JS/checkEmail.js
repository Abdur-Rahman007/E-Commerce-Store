function checkEmail() {
    var email = document.getElementById("email").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.available) {
                document.getElementById("emailAvailability").innerHTML = "Email is available";
                document.getElementById("emailAvailability").style.color = "green";
            } else {
                document.getElementById("emailAvailability").innerHTML = "Email is already registered";
                document.getElementById("emailAvailability").style.color = "red";
            }
        }
    };
    xhttp.open("POST", "../Controller/checkEmailAjax.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("email=" + email);
}
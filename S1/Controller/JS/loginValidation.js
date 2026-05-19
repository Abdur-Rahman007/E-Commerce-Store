document.getElementById("loginForm").addEventListener("submit", function(e) {
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;

    var emailError = document.getElementById("emailError");
    var passwordError = document.getElementById("passwordError");


    emailError.textContent = "";
    passwordError.textContent = "";

    var hasError = false;

    if (!email) {
        emailError.textContent = "Email is required";
        hasError = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailError.textContent = "Enter a valid email address.";
        hasError = true;
    }

    if (!password) {
        passwordError.textContent = "Password is required";
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
    }
});

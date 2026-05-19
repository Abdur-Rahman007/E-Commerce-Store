document.getElementById("registerForm").addEventListener("submit", function(e) {

    var name = document.getElementById("name").value.trim();
    var email = document.getElementById("email").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var password = document.getElementById("password").value;
    var confirm  = document.getElementById("confirm_password").value;

    var nameError = document.getElementById("nameError");
    var emailError = document.getElementById("emailError");
    var phoneError = document.getElementById("phoneError");
    var passwordError = document.getElementById("passwordError");
    var confirmError = document.getElementById("confirmError");

    nameError.textContent = "";
    emailError.textContent = "";
    phoneError.textContent = "";
    passwordError.textContent = "";
    confirmError.textContent = "";

    var hasError = false;

    if (!name) {
        nameError.textContent = "Full name is required";
        hasError = true;
    } else if (name.length < 3) {
        nameError.textContent = "Name must be at least 3 characters";
        hasError = true;
    }

    if (!email) {
        emailError.textContent = "Email is required.";
        hasError = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailError.textContent = "Enter a valid email address";
        hasError = true;
    } else {
        var availMsg = document.getElementById("emailAvailability");
        if (availMsg && availMsg.textContent === "Email is already registered") {
            emailError.textContent = "This email is already registered";
            hasError = true;
        }
    }

    if (phone && !/^[0-9+\-\s()]{7,15}$/.test(phone)) {
        phoneError.textContent = "Invalid phone number format.";
        hasError = true;
    }

    if (!password) {
        passwordError.textContent = "Password is required.";
        hasError = true;
    } else if (password.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters.";
        hasError = true;
    }

    if (!confirm) {
        confirmError.textContent = "Please confirm your password.";
        hasError = true;
    } else if (password !== confirm) {
        confirmError.textContent = "Passwords do not match.";
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
    }
});

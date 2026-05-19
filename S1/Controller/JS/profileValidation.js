document.getElementById("profileForm").addEventListener("submit", function(e) {

    var name  = document.getElementById("prof_name").value.trim();
    var email = document.getElementById("prof_email").value.trim();
    var phone = document.getElementById("prof_phone").value.trim();

    var nameError  = document.getElementById("profNameError");
    var emailError = document.getElementById("profEmailError");
    var phoneError = document.getElementById("profPhoneError");

    nameError.textContent = "";
    emailError.textContent = "";
    phoneError.textContent = "";

    var hasError = false;

    if (!name) {
        nameError.textContent = "Full name is required.";
        hasError = true;
    }

    if (!email) {
        emailError.textContent = "Email is required.";
        hasError = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailError.textContent = "Enter a valid email address.";
        hasError = true;
    }

    if (phone && !/^[0-9+\-\s()]{7,15}$/.test(phone)) {
        phoneError.textContent = "Invalid phone number format.";
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
    }
});

// ── Password Change Form ─────────────────────────────────────
document.getElementById("passwordForm").addEventListener("submit", function(e) {

    var newPw   = document.getElementById("new_password").value;
    var confirm = document.getElementById("confirm_new").value;

    var newPwError   = document.getElementById("pwNewError");
    var confirmError = document.getElementById("pwConfirmError");

    newPwError.textContent   = "";
    confirmError.textContent = "";

    var hasError = false;

    if (!newPw) {
        newPwError.textContent = "New password is required.";
        hasError = true;
    } else if (newPw.length < 8) {
        newPwError.textContent = "New password must be at least 8 characters.";
        hasError = true;
    }

    if (!confirm) {
        confirmError.textContent = "Please confirm your new password.";
        hasError = true;
    } else if (newPw !== confirm) {
        confirmError.textContent = "Passwords do not match.";
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
    }
});

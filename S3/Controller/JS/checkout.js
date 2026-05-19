/**
 * S3/Controller/JS/checkout.js
 * Client-side validation for checkout form
 */

// ─────────────────────────────────────────────────
//  TOGGLE NEW ADDRESS TEXTAREA
// ─────────────────────────────────────────────────
function toggleNewAddress(radio) {
    const block = document.getElementById("new-address-block");
    if (block) {
        block.style.display = radio.value === "new" ? "block" : "none";
    }
}

// Also hide new address block when a saved address is selected
document.querySelectorAll('input[name="address_choice"]').forEach(function (radio) {
    radio.addEventListener("change", function () {
        const block = document.getElementById("new-address-block");
        if (block) {
            block.style.display = this.value === "new" ? "block" : "none";
        }
    });
});

// ─────────────────────────────────────────────────
//  FORM VALIDATION ON SUBMIT
// ─────────────────────────────────────────────────
document.getElementById("checkout-form").addEventListener("submit", function (e) {
    let valid = true;

    // Clear previous errors
    document.getElementById("addr-error").textContent = "";
    document.getElementById("pay-error").textContent  = "";

    // ── Validate address ───────────────────────
    const addrRadios = document.querySelectorAll('input[name="address_choice"]');
    const chosen     = [...addrRadios].find(function (r) { return r.checked; });

    if (!chosen) {
        document.getElementById("addr-error").textContent = "Please select a shipping address.";
        valid = false;
    } else if (chosen.value === "new") {
        const newAddr = document.getElementById("new_address").value.trim();
        if (newAddr.length < 10) {
            document.getElementById("addr-error").textContent = "Please enter a valid address (at least 10 characters).";
            valid = false;
        }
    }

    // ── Validate payment method ────────────────
    const payRadios = document.querySelectorAll('input[name="payment_method"]');
    const payChosen = [...payRadios].find(function (r) { return r.checked; });

    if (!payChosen) {
        document.getElementById("pay-error").textContent = "Please choose a payment method.";
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    }
});
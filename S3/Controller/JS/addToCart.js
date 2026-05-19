/**
 * S3/Controller/JS/addToCart.js
 * Handles Add to Cart button clicks on catalogue.php
 */

function addToCart(productId, btn) {
    btn.disabled    = true;
    btn.textContent = "Adding...";

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const data = JSON.parse(this.responseText);

            btn.disabled    = false;
            btn.textContent = "Add to Cart";

            if (data.error) {
                showToast(data.error, true);
            } else {
                // Update cart badge in navbar
                const badge = document.getElementById("cart-count");
                if (badge) badge.textContent = data.total_items;
                showToast("Item added to cart!");
            }
        }
    };

    xhttp.open("POST", "../Controller/addToCart.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("product_id=" + encodeURIComponent(productId));
}

function showToast(msg, isError) {
    const toast            = document.getElementById("toast");
    toast.textContent      = msg;
    toast.style.background = isError ? "#dc3545" : "#1a1a2e";
    toast.classList.add("show");
    setTimeout(function () { toast.classList.remove("show"); }, 3000);
}
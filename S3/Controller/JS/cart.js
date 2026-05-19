/**
 * S3/Controller/JS/cart.js
 * Handles: + / - quantity, remove item — all via AJAX, no full reload
 */

// ─────────────────────────────────────────────────
//  UPDATE QUANTITY  (+1 or -1)
// ─────────────────────────────────────────────────
function updateQty(productId, delta) {
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const data = JSON.parse(this.responseText);

            if (data.error) {
                showToast(data.error, true);
                return;
            }

            if (data.removed) {
                // Qty hit 0 — remove the row from the DOM
                const row = document.getElementById("row-" + productId);
                if (row) row.remove();
            } else {
                // Update qty display and line total for this row
                document.getElementById("qty-"  + productId).textContent = data.new_qty;
                document.getElementById("line-" + productId).textContent = "৳" + data.line_total;
            }

            // Always update grand total and badge
            document.getElementById("grand-total").textContent        = "৳" + data.grand_total;
            document.getElementById("cart-count").textContent         = data.total_items;
            document.getElementById("cart-count-summary").textContent = data.total_items;

            checkEmptyCart();
        }
    };

    xhttp.open("POST", "../Controller/cartUpdate.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("product_id=" + productId + "&delta=" + delta);
}

// ─────────────────────────────────────────────────
//  REMOVE ITEM
// ─────────────────────────────────────────────────
function removeItem(productId) {
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const data = JSON.parse(this.responseText);

            if (data.error) {
                showToast(data.error, true);
                return;
            }

            // Remove the row from DOM
            const row = document.getElementById("row-" + productId);
            if (row) row.remove();

            // Update totals
            document.getElementById("grand-total").textContent        = "৳" + data.grand_total;
            document.getElementById("cart-count").textContent         = data.total_items;
            document.getElementById("cart-count-summary").textContent = data.total_items;

            showToast("Item removed.");
            checkEmptyCart();
        }
    };

    xhttp.open("POST", "../Controller/cartRemove.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("product_id=" + productId);
}

// ─────────────────────────────────────────────────
//  CHECK IF CART IS NOW EMPTY
// ─────────────────────────────────────────────────
function checkEmptyCart() {
    const rows = document.querySelectorAll(".cart-item-row");
    if (rows.length === 0) {
        document.getElementById("cart-content").innerHTML = `
            <div class="empty-cart">
                <p>🛒 Your cart is empty.</p>
                <a href="catalogue.php" class="btn btn-primary" style="width:auto;margin-top:1rem;">
                    Shop Now
                </a>
            </div>`;
    }
}

// ─────────────────────────────────────────────────
//  TOAST
// ─────────────────────────────────────────────────
function showToast(msg, isError) {
    const toast        = document.getElementById("toast");
    toast.textContent  = msg;
    toast.style.background = isError ? "#dc3545" : "#1a1a2e";
    toast.classList.add("show");
    setTimeout(function () { toast.classList.remove("show"); }, 3000);
}
/**
 * search.js
 * Handles: live keyword search, category filter, card rendering
 */

// ─────────────────────────────────────────────────
//  RENDER ONE PRODUCT CARD
// ─────────────────────────────────────────────────
function renderCard(p) {
    const img = p.primary_image_path
        ? `<img src="../${p.primary_image_path}" alt="${escHtml(p.name)}" loading="lazy">`
        : `<div class="no-img">No Image</div>`;

    const cartBtn = parseInt(p.stock_qty) > 0
        ? `<button class="btn btn-primary" onclick="addToCart(${p.id}, this)">Add to Cart</button>`
        : `<button class="btn btn-disabled" disabled>Out of Stock</button>`;

    return `
    <div class="product-card">
        <a href="productDetail.php?id=${p.id}">${img}</a>
        <div class="card-body">
            <div class="card-category">${escHtml(p.category_name || 'Uncategorized')}</div>
            <div class="card-title">${escHtml(p.name)}</div>
            <div class="card-price">৳${parseFloat(p.price).toFixed(2)}</div>
            ${cartBtn}
        </div>
    </div>`;
}

// ─────────────────────────────────────────────────
//  KEYWORD SEARCH  (called by onkeyup)
// ─────────────────────────────────────────────────
function search() {
    const keyword    = document.getElementById("search-input").value.trim();
    const categoryId = document.getElementById("category-filter").value;

    const body = "keyword="      + encodeURIComponent(keyword)
               + "&category_id=" + encodeURIComponent(categoryId);

    sendRequest(body, keyword);
}

// ─────────────────────────────────────────────────
//  CATEGORY FILTER  (called by onchange)
// ─────────────────────────────────────────────────
function filterByCategory() {
    const categoryId = document.getElementById("category-filter").value;
    const keyword    = document.getElementById("search-input").value.trim();

    const body = "keyword="      + encodeURIComponent(keyword)
               + "&category_id=" + encodeURIComponent(categoryId);

    sendRequest(body, keyword);
}

// ─────────────────────────────────────────────────
//  SHARED AJAX FUNCTION
// ─────────────────────────────────────────────────
function sendRequest(body, keyword) {
    const grid  = document.getElementById("product-grid");
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const data = JSON.parse(this.responseText);

            if (data.error) {
                grid.innerHTML = `<p style="color:red;grid-column:1/-1;">${data.error}</p>`;
                return;
            }

            if (data.products.length === 0) {
                grid.innerHTML = `<p style="color:#888;grid-column:1/-1;text-align:center;padding:2rem;">No products found.</p>`;
                return;
            }

            grid.innerHTML = data.products.map(renderCard).join("");
        }
    };

    xhttp.open("POST", "../Controller/search.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(body);
}

// ─────────────────────────────────────────────────
//  HTML ESCAPE  (prevent XSS)
// ─────────────────────────────────────────────────
function escHtml(str) {
    const d = document.createElement("div");
    d.textContent = str || "";
    return d.innerHTML;
}
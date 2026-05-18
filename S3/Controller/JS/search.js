/**
 * search.js
 * Called on every keyup from the search input in catalogue.php.
 * Sends the keyword to Controller/API/search.php via AJAX,
 * gets JSON back, and re-renders the product grid.
 */

function search() {
    const keyword = document.getElementById("search-input").value.trim();
    const grid    = document.getElementById("product-grid");

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const data = JSON.parse(this.responseText);

            if (data.error) {
                grid.innerHTML = `<p style="color:red;">${data.error}</p>`;
                return;
            }

            if (data.products.length === 0) {
                grid.innerHTML = `<p style="color:#888;grid-column:1/-1;text-align:center;padding:2rem;">No products found for "<strong>${escHtml(keyword)}</strong>".</p>`;
                return;
            }

            // Rebuild the grid cards
            grid.innerHTML = data.products.map(function (p) {
                const img = p.primary_image_path
                    ? `<img src="../${p.primary_image_path}" alt="${escHtml(p.name)}" loading="lazy">`
                    : `<div class="no-img">No Image</div>`;

                return `
                <div class="product-card">
                    <a href="productDetail.php?id=${p.id}">${img}</a>
                    <div class="card-body">
                        <div class="card-category">${escHtml(p.category_name || 'Uncategorized')}</div>
                        <div class="card-title">${escHtml(p.name)}</div>
                        <div class="card-price">৳${parseFloat(p.price).toFixed(2)}</div>
                        <button class="btn btn-primary" onclick="addToCart(${p.id}, this)">Add to Cart</button>
                    </div>
                </div>`;
            }).join("");
        }
    };

    xhttp.open("POST", "../Controller/API/search.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("keyword=" + encodeURIComponent(keyword));
}

// Safely escape HTML to prevent XSS in rendered cards
function escHtml(str) {
    const d = document.createElement("div");
    d.textContent = str || "";
    return d.innerHTML;
}
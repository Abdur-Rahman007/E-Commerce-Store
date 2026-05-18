// ==========================
// SELECT ELEMENTS
// ==========================
const searchInput = document.getElementById("search-input");
const categoryFilter = document.getElementById("category-filter");
const productGrid = document.getElementById("product-grid");

// ==========================
// RENDER PRODUCTS
// ==========================
function renderProducts(products) {

    productGrid.innerHTML = "";

    if (products.length === 0) {
        productGrid.innerHTML = "<p>No products found</p>";
        return;
    }

    products.forEach(p => {

        productGrid.innerHTML += `
            <div class="product-card">

                <a href="productDetail.php?id=${p.id}">
                    ${
                        p.primary_image_path
                        ? `<img src="../${p.primary_image_path}">`
                        : `<div class="no-img">No Image</div>`
                    }
                </a>

                <div class="card-body">

                    <div class="card-category">
                        ${p.category_name ?? "Uncategorized"}
                    </div>

                    <div class="card-title">
                        ${p.name}
                    </div>

                    <div class="card-price">
                        ৳${p.price}
                    </div>

                    <button onclick="addToCart(${p.id}, this)" class="btn btn-primary">
                        Add to Cart
                    </button>

                </div>
            </div>
        `;
    });
}

// ==========================
// FETCH PRODUCTS (SEARCH)
// ==========================
async function searchProducts(keyword) {

    const res = await fetch(`../api/products/search.php?q=${keyword}`);
    const data = await res.json();

    renderProducts(data);
}

// ==========================
// FETCH PRODUCTS (CATEGORY)
// ==========================
async function filterByCategory(categoryId) {

    const url = categoryId
        ? `../api/products/index.php?category_id=${categoryId}`
        : `../api/products/index.php`;

    const res = await fetch(url);
    const data = await res.json();

    renderProducts(data);
}

// ==========================
// EVENT: SEARCH INPUT
// ==========================
searchInput.addEventListener("input", function () {
    const keyword = this.value.trim();
    searchProducts(keyword);
});

// ==========================
// EVENT: CATEGORY FILTER
// ==========================
categoryFilter.addEventListener("change", function () {
    const categoryId = this.value;
    filterByCategory(categoryId);
});
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Products - T-STORE</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .page-header {
        text-align: center;
        margin: 30px 0;
    }
    .page-header h2 {
      margin-top: 0;
      margin-bottom: 15px;
      color: #673ab7;
      font-size: 2rem;
    }
    .back-btn {
        display: inline-block;
        padding: 8px 15px;
        background-color: #6c757d;
        color: white !important;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        font-weight: normal;
    }
    .back-btn:hover {
        background-color: #5a6268;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      background-color: #f8f6fc;
      margin: 0;
      padding: 0;
    }

    h2 {
      text-align: center;
      margin-top: 30px;
      color: #673ab7;
      font-size: 2rem;
    }

    #product-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 30px;
      width: 90%;
      margin: auto;
    }

    .product-box {
      background-color: #fff;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      text-align: center;
    }

    .product-box img {
      width: 150px;
      height: auto;
      border-radius: 4px;
    }

    .product-box h3 {
      color: #333;
      font-size: 18px;
      margin: 10px 0 5px;
    }

    .product-box p {
      color: #555;
      margin: 5px 0 10px;
    }

    .product-box input[type="number"] {
      width: 50px;
      padding: 5px;
      text-align: center;
      margin-right: 5px;
    }

    .product-box button {
      background-color: #ffd700;
      border: 1px solid #d4af37;
      padding: 8px 15px;
      color: #000000;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.2s ease;
    }

    .product-box button:hover {
      background-color: #e0a800;
    }

    /* Search Bar CSS */
    .search-bar-container {
      max-width: 500px;
      margin: 20px auto 10px;
      display: flex;
      justify-content: center;
      padding: 0 15px;
    }
    .search-input-field {
      width: 100%;
      padding: 12px 20px;
      border: 2px solid #ccc;
      border-radius: 30px;
      font-size: 16px;
      outline: none;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      background-color: #fff;
    }
    .search-input-field:focus {
      border-color: darkorange;
      box-shadow: 0 4px 10px rgba(255, 140, 0, 0.25);
    }
    .no-products-msg {
      grid-column: 1 / -1;
      text-align: center;
      font-size: 18px;
      color: #555;
      margin-top: 40px;
    }
    body {
      padding-top: 100px;
    }
  </style>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="page-header">
    <h2>🛍️ Our Products</h2>
    <div style="margin-top: 10px;">
      <a href="index.php" class="back-btn">← Back to Home</a>
      <?php if (isset($_SESSION['id'])): ?>
        <a href="profile.php" class="back-btn" style="margin-left: 10px;">👤 My Profile</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Search Input Container -->
  <div class="search-bar-container">
    <input type="text" id="local-search-input" class="search-input-field" placeholder="🔍 Search products by name or description...">
  </div>

  <div id="product-container">Loading products...</div>

  <script>
    let allProducts = [];

    // Parse URL parameter if any
    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('search') || '';
    const searchInput = document.getElementById("local-search-input");
    searchInput.value = initialQuery;

    // Fetch products
    fetch("../backend/get_products.php")
      .then(response => {
        if (!response.ok) {
          throw new Error("Network response was not OK");
        }
        return response.json();
      })
      .then(data => {
        allProducts = data;
        renderProducts(allProducts);
        
        // Initial filter if search query parameter exists
        if (initialQuery !== '') {
          filterProducts(initialQuery);
        }
      })
      .catch(error => {
        document.getElementById("product-container").innerHTML = "❌ Failed to load products.";
        console.error("Fetch error:", error);
      });

    // Render products function
    function renderProducts(productsList) {
      const container = document.getElementById("product-container");
      container.innerHTML = '';

      if (productsList.length === 0) {
        container.innerHTML = '<div class="no-products-msg">❌ No products found matching your search.</div>';
        return;
      }

      productsList.forEach(product => {
        const div = document.createElement("div");
        div.className = "product-box";
        div.innerHTML = `
          <h3>${product.name}</h3>
          <img src="${product.image}" alt="${product.name}">
          <p>LKR ${product.price}</p>
          <form action="../backend/add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="${product.id}">
            <input type="number" name="quantity" value="1" min="1">
            <button type="submit">Add to Cart</button>
          </form>
        `;
        container.appendChild(div);
      });
    }

    // Filter products function
    function filterProducts(query) {
      const lowerQuery = query.toLowerCase().trim();
      if (lowerQuery === '') {
        renderProducts(allProducts);
        return;
      }

      const filtered = allProducts.filter(product => {
        const nameMatch = product.name ? product.name.toLowerCase().includes(lowerQuery) : false;
        const descMatch = product.description ? product.description.toLowerCase().includes(lowerQuery) : false;
        return nameMatch || descMatch;
      });

      renderProducts(filtered);
    }

    // Real-time filtering event listener
    searchInput.addEventListener("input", (e) => {
      filterProducts(e.target.value);
    });
  </script>
</body>
</html>

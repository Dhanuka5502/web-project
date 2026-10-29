<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T-STORE - Premium T-Shirts & Custom Apparel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="homepage.css">
    <script src="script.js" defer></script>
    <!-- Boxicons CSS -->
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<!-- Navbar -->
<?php include 'nav.php'; ?>

<!-- Home / Hero Section -->
<section class="home" id="home">
    <div class="home-text">
        <h1>Dress with<br>confidence</h1>
        <p>At T-STORE, we believe that fashion is a way to express yourself. Whether you're looking for casual wear, customized prints, or premium fabrics, we've got something for everyone. Make every outfit uniquely yours.</p>
        <a href="products.php" class="btn">Shop Now</a>
    </div>
    <div class="home-img">
        <img src="img/bg/home.png" alt="Home Image">
    </div>
</section>

<!-- Slideshow Section -->
<section class="slideshow-section">
    <h2>New Arrivals</h2>
    <div class="slideshow-container">
        <div class="mySlides fade">
            <img src="img/slideshow/slide1.webp" alt="Slide 1">
        </div>
        <div class="mySlides fade">
            <img src="img/slideshow/slide2.webp" alt="Slide 2">
        </div>
        <div class="mySlides fade">
            <img src="img/slideshow/slide3.jpg" alt="Slide 3">
        </div>
    </div>
</section>

<script>
    let slideIndex = 0;
    
    function showSlides() {
        let slides = document.getElementsByClassName("mySlides");
    
        // Hide all slides
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
    
        // Show the next slide
        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1 } 
        slides[slideIndex - 1].style.display = "block";
    
        // Change every 2s
        setTimeout(showSlides, 3000);
    }
    
    showSlides();
</script>

<!-- Customize Section -->  
<section class="customize" id="customize">
    <div class="customize-img">
        <img src="img/customize.jpg" alt="Customize">
    </div>
    <div class="customize-text">
        <h2>Customizer Studio</h2>
        <p>Express your unique style with our customizable T-shirts. Choose from a variety of colors, designs, and custom text to create a look that truly represents you. Whether it's a personal statement, a special gift, or a brand logo, we bring your ideas to life with high-quality prints and premium fabrics. Start designing today!</p>
        <a href="customize.php" class="btn">Start Designing</a>
    </div>
</section>

<!-- Products Section -->
<section class="products" id="products">
    <div class="heading">
       <h2>Our Featured Products</h2>
    </div>
    <!-- Container for dynamic products -->
    <div class="product-container" id="home-product-container">
        <p style="text-align: center; color: var(--text-muted);">Loading featured products...</p>
    </div>
</section>

<script>
    // Dynamically load products for the homepage
    document.addEventListener('DOMContentLoaded', function() {
        fetch('../backend/get_products.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('home-product-container');
                container.innerHTML = ''; // Clear existing content

                if (data.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: var(--text-muted);">No products available at this time.</p>';
                    return;
                }

                // Display only the first 5 products on the homepage
                const productsToShow = data.slice(0, 5);

                productsToShow.forEach(product => {
                    const box = document.createElement('div');
                    box.className = 'box';
                    
                    // Fallback image handling
                    const imagePath = product.image ? product.image : 'img/logo.png';
                    
                    box.innerHTML = `
                        <img src="${imagePath}" alt="${product.name}">
                        <h3>${product.name}</h3>
                        <div class="content">
                            <span>LKR ${product.price}</span>
                            <form action="../backend/add_to_cart.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="${product.id}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn">Add to Cart</button>
                            </form>
                        </div>
                    `;
                    container.appendChild(box);
                });
            })
            .catch(error => {
                const container = document.getElementById('home-product-container');
                container.innerHTML = '<p style="text-align: center; color: var(--text-muted);">Could not load products at this time.</p>';
                console.error('Error fetching products for homepage:', error);
            });
    });
</script>

<!--Customers / Reviews Section-->
<section class="customers" id="customers">
    <div class="heading">
        <h2>What People Say</h2>
    </div>
    <!--container-->
    <div class="customers-container">
        <div class="box">
            <div class="stars">
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bx-star'></i>
            </div>
            <p>"The customization tool is extremely fun to use. The print quality on my custom shirt turned out great, and it washed really well without fading!"</p>
            <h2>Pasindu Shehan</h2>
            <img src="img/customers/cus2.jpg" alt="Customer Avatar">
        </div>
        <div class="box">
            <div class="stars">
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
            </div>
            <p>"The T-shirts from T-STORE are incredibly soft and comfortable. The fit is perfect, and I love the unique designs. Definitely my go-to store for casual wear."</p>
            <h2>Shehan Chanuka</h2>
            <img src="img/customers/cus1.jpg" alt="Customer Avatar">
        </div>
        <div class="box">
            <div class="stars">
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
                <i class='bx bxs-star'></i>
            </div>
            <p>"I was surprised by how quickly my order arrived! The packaging was neat, and the T-shirt looked exactly like the online preview. Excellent service!"</p>
            <h2>U.D.P.S.Chanuka</h2>
            <img src="img/customers/cus3.jpg" alt="Customer Avatar">
        </div>
    </div>
</section>

<!--Footer Section-->
<section class="footer">
    <div class="footer-box">
        <h3>T-STORE</h3>
        <p>Join our community of trendsetters and shop the latest designs today! Wear confidence, wear T-STORE.</p>
        <div class="social">
            <a href="#"><i class='bx bxl-whatsapp-square'></i></a>
            <a href="#"><i class='bx bx-envelope'></i></a>
            <a href="#"><i class='bx bxs-phone-call'></i></a>
        </div>
    </div>
    <div class="footer-box">
        <h3>Support</h3>
        <li><a href="products.php">Products</a></li>
        <li><a href="contact.php">Help & Support</a></li>
        <li><a href="#">Return Policy</a></li>
        <li><a href="#">Terms Of Use</a></li>
    </div>
    <div class="footer-box">
        <h3>View Guides</h3>
        <li><a href="#">Size charts</a></li>
        <li><a href="#">T-shirt material details</a></li>
        <li><a href="#">Washing instructions</a></li>
        <li><a href="#">FAQs</a></li>
    </div>
    <div class="footer-box">
        <h3>Contact</h3>
        <span><i class='bx bx-map-alt'></i>90, Gangodawila Road, Mahara, Hambanthota.</span>
        <span><i class='bx bxs-phone'></i>071234567</span>
    </div>
</section>
</body>
</html>

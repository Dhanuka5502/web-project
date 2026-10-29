<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if we are inside the 'backend' folder
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$is_backend = ($current_dir === 'backend');

// Define path prefixes accordingly
$prefix_frontend = $is_backend ? '../frontend/' : './';
$prefix_backend = $is_backend ? './' : '../backend/';
$prefix_img = $is_backend ? '../frontend/' : './';
?>
<header>
    <a href="<?php echo $prefix_frontend; ?>index.php" class="logo">
        <img src="<?php echo $prefix_img; ?>img/logo.png" alt="Logo">
    </a>
    <!--Menu Icon-->
    <i class='bx bx-menu' id="menu-icon"></i>
    <ul class="navbar">
        <li><a href="<?php echo $prefix_frontend; ?>index.php#home">Home</a></li>
        <li><a href="<?php echo $prefix_frontend; ?>customize.php">Customize</a></li>
        <li><a href="<?php echo $prefix_frontend; ?>products.php">Products</a></li>
        <li><a href="<?php echo $prefix_frontend; ?>contact.php">Customer Care</a></li>
        <?php if (isset($_SESSION['id'])): ?>
            <li><a href="<?php echo $prefix_frontend; ?>profile.php">My Profile</a></li>
        <?php endif; ?>
    </ul>
    <div class="header-icon">
        <i class='bx bx-search-alt' id="search-icon"></i>
        <?php if (isset($_SESSION['id'])): ?>
            <a href="<?php echo $prefix_frontend; ?>profile.php"><i class='bx bx-user' id="profile-icon"></i></a>
            <a href="<?php echo $prefix_backend; ?>logout.php"><i class='bx bx-log-out' id="logout-icon"></i></a>
        <?php else: ?>
            <a href="<?php echo $prefix_frontend; ?>login.html"><i class='bx bx-log-in' id="log-in-icon"></i></a>
        <?php endif; ?>
        <a href="<?php echo $prefix_backend; ?>view_cart.php"><i class='bx bx-cart' id="cart-icon"></i></a>
    </div>
    <div class="search-box" style="display: none;">
        <form action="<?php echo $prefix_frontend; ?>products.php" method="GET">
            <input type="search" name="search" placeholder="Search....">
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchIcon = document.getElementById('search-icon');
        const searchBox = document.querySelector('.search-box');
        if (searchIcon && searchBox) {
            searchIcon.addEventListener('click', function(e) {
                e.preventDefault();
                if (searchBox.style.display === 'none' || searchBox.style.display === '') {
                    searchBox.style.display = 'block';
                    setTimeout(() => {
                        searchBox.classList.add('active');
                    }, 10);
                    const input = searchBox.querySelector('input');
                    if (input) input.focus();
                } else {
                    searchBox.classList.remove('active');
                    setTimeout(() => {
                        if (!searchBox.classList.contains('active')) {
                            searchBox.style.display = 'none';
                        }
                    }, 300);
                }
            });
        }

        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        if (menuIcon && navbar) {
            menuIcon.addEventListener('click', function() {
                menuIcon.classList.toggle('bx-x');
                navbar.classList.toggle('active');
            });
        }
    });
    </script>
</header> 
let slideIndex = 0;

function showSlides() {
    let slides = document.getElementsByClassName("mySlides");

    // Hide
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    // Show the next slide
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }//when in last slides go back to first one//
    slides[slideIndex - 1].style.display = "block";

    // Change every 2s
    setTimeout(showSlides, 2000);
}

// Initializing
showSlides();

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

let search = document.querySelector('.search-box');

document.querySelector('#search-icon').onclick = ( )=> {
    search.classList.toggle('active');
};

// Check login status and update navigation
function checkLoginStatus() {
    fetch('../backend/check_login_status.php')
        .then(response => response.json())
        .then(data => {
            const loginLink = document.getElementById('login-link');
            const profileLink = document.getElementById('profile-link');
            const logoutLink = document.getElementById('logout-link');
            
            if (data.logged_in) {
                // User is logged in
                if (loginLink) loginLink.style.display = 'none';
                if (profileLink) profileLink.style.display = 'inline-block';
                if (logoutLink) logoutLink.style.display = 'inline-block';
            } else {
                // User is not logged in
                if (loginLink) loginLink.style.display = 'inline-block';
                if (profileLink) profileLink.style.display = 'none';
                if (logoutLink) logoutLink.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error checking login status:', error);
        });
}

// Check login status when page loads
document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
});

// Check login status every 30 seconds to keep it updated
setInterval(checkLoginStatus, 30000);

// Navbar menu toggle
let menu = document.querySelector('#menu-icon');
let navbar = document.querySelector('.navbar');

menu.onclick = () => {
    menu.classList.toggle('bx-x');
    navbar.classList.toggle('active');
}

window.onscroll = () => {
    menu.classList.remove('bx-x');
    navbar.classList.remove('active');
}


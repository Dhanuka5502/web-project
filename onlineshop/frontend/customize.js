// Customize Page JavaScript

// Global variables
let currentTshirtColor = '#ffffff';
let currentText = '';
let currentDesign = '';
let currentTextColor = '#000000';
let currentFontSize = '20';
let currentSize = 'S';
let currentQuantity = 1;
let basePrice = 2500;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Set initial active color
    document.querySelector('.color-option[data-color="#ffffff"]').classList.add('active');
    
    // Update price display
    updatePrice();
    
    // Add event listeners
    addEventListeners();
    
    // Check login status and update navigation
    checkLoginStatus();
});

// Add event listeners
function addEventListeners() {
    // Text input
    document.getElementById('custom-text').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            addText();
        }
    });
    
    // Font size change
    document.getElementById('font-size').addEventListener('change', function() {
        currentFontSize = this.value;
        updateTextDisplay();
    });
    
    // Text color change
    document.getElementById('text-color').addEventListener('change', function() {
        currentTextColor = this.value;
        updateTextDisplay();
    });
    
    // Quantity input
    document.getElementById('quantity').addEventListener('change', function() {
        currentQuantity = parseInt(this.value);
        updatePrice();
    });
    
    // Size selection
    document.querySelectorAll('input[name="size"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentSize = this.value;
        });
    });
    
    // Add to cart button
    document.getElementById('add-to-cart-btn').addEventListener('click', function() {
        addToCart();
    });
}

// Change T-shirt color
function changeTshirtColor(color) {
    currentTshirtColor = color;
    
    // Update T-shirt base color
    document.getElementById('tshirt-base').style.backgroundColor = color;
    
    // Update active color option
    document.querySelectorAll('.color-option').forEach(option => {
        option.classList.remove('active');
    });
    document.querySelector(`.color-option[data-color="${color}"]`).classList.add('active');
    
    // Adjust text color for better visibility on dark backgrounds
    if (isDarkColor(color)) {
        document.getElementById('text-color').value = '#ffffff';
        currentTextColor = '#ffffff';
        updateTextDisplay();
    }
}

// Check if color is dark
function isDarkColor(color) {
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness < 128;
}

// Add custom text
function addText() {
    const textInput = document.getElementById('custom-text');
    const text = textInput.value.trim();
    
    if (text) {
        currentText = text;
        updateTextDisplay();
        textInput.value = '';
        
        // Show success message
        showMessage('Text added successfully!', 'success');
    } else {
        showMessage('Please enter some text!', 'error');
    }
}

// Update text display
function updateTextDisplay() {
    const textElement = document.getElementById('tshirt-text');
    
    if (currentText) {
        textElement.innerHTML = currentText;
        textElement.style.color = currentTextColor;
        textElement.style.fontSize = currentFontSize + 'px';
        textElement.style.fontWeight = 'bold';
        textElement.style.textShadow = '1px 1px 2px rgba(0,0,0,0.3)';
    } else {
        textElement.innerHTML = '';
    }
}

// Add design template
function addDesign(designType) {
    const designElement = document.getElementById('tshirt-design');
    
    if (designType === 'clear') {
        currentDesign = '';
        designElement.innerHTML = '';
        showMessage('Design cleared!', 'success');
        return;
    }
    
    const designs = {
        'heart': '❤️',
        'star': '⭐',
        'smile': '😊',
        'peace': '✌️',
        'music': '🎵'
    };
    
    if (designs[designType]) {
        currentDesign = designs[designType];
        designElement.innerHTML = currentDesign;
        designElement.style.fontSize = '32px';
        showMessage('Design added!', 'success');
    }
}

// Change quantity
function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let newQuantity = parseInt(quantityInput.value) + change;
    
    if (newQuantity >= 1 && newQuantity <= 10) {
        quantityInput.value = newQuantity;
        currentQuantity = newQuantity;
        updatePrice();
    }
}

// Update price display
function updatePrice() {
    const totalPrice = basePrice * currentQuantity;
    document.getElementById('custom-price').textContent = totalPrice.toLocaleString();
}

// Reset design
function resetDesign() {
    // Reset T-shirt color
    changeTshirtColor('#ffffff');
    
    // Clear text
    currentText = '';
    updateTextDisplay();
    
    // Clear design
    currentDesign = '';
    document.getElementById('tshirt-design').innerHTML = '';
    
    // Reset text color
    document.getElementById('text-color').value = '#000000';
    currentTextColor = '#000000';
    
    // Reset font size
    document.getElementById('font-size').value = '20';
    currentFontSize = '20';
    
    // Reset quantity
    document.getElementById('quantity').value = '1';
    currentQuantity = 1;
    updatePrice();
    
    // Reset size
    document.querySelector('input[name="size"][value="S"]').checked = true;
    currentSize = 'S';
    
    showMessage('Design reset successfully!', 'success');
}

// Check login status and update navigation
function checkLoginStatus() {
    fetch('../backend/check_login_status.php', { credentials: 'same-origin' })
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

// Check if user is logged in
function isUserLoggedIn() {
    // Use a synchronous check by making a quick API call
    return new Promise((resolve) => {
        fetch('../backend/check_login_status.php', { credentials: 'same-origin' })
            .then(response => response.json())
            .then(data => {
                resolve(data.logged_in);
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                resolve(false);
            });
    });
}

// Add to cart (updated to handle async login check)
async function addToCart() {
    // Check if user is logged in
    const userLoggedIn = await isUserLoggedIn();
    if (!userLoggedIn) {
        showMessage('Please login to add custom items to cart!', 'error');
        return;
    }
    
    // Validate design
    if (!currentText && !currentDesign) {
        showMessage('Please add some text or design to your T-shirt!', 'error');
        return;
    }
    
    // Create custom product data
    const customProduct = {
        name: 'Custom T-Shirt',
        price: basePrice,
        quantity: currentQuantity,
        size: currentSize,
        color: currentTshirtColor,
        text: currentText,
        design: currentDesign,
        textColor: currentTextColor,
        fontSize: currentFontSize,
        isCustom: true
    };
    
    // Send to backend
    addCustomToCart(customProduct);
}

// Add custom product to cart
function addCustomToCart(productData) {
    fetch('../backend/add_custom_to_cart.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(productData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Custom T-shirt added to cart successfully!', 'success');
            // Optionally redirect to cart
            setTimeout(() => {
                window.location.href = '../backend/view_cart.php';
            }, 1500);
        } else {
            showMessage(data.message || 'Failed to add to cart!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while adding to cart!', 'error');
    });
}

// Show message
function showMessage(message, type) {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        max-width: 300px;
    `;
    
    // Set background color based on type
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#4CAF50';
    } else if (type === 'error') {
        messageDiv.style.backgroundColor = '#f44336';
    }
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations for messages
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to add text
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        addText();
    }
    
    // Escape to reset design
    if (e.key === 'Escape') {
        resetDesign();
    }
});

// Save design to localStorage (for persistence)
function saveDesign() {
    const design = {
        color: currentTshirtColor,
        text: currentText,
        design: currentDesign,
        textColor: currentTextColor,
        fontSize: currentFontSize,
        size: currentSize,
        quantity: currentQuantity
    };
    localStorage.setItem('customTshirtDesign', JSON.stringify(design));
}

// Load design from localStorage
function loadDesign() {
    const saved = localStorage.getItem('customTshirtDesign');
    if (saved) {
        const design = JSON.parse(saved);
        changeTshirtColor(design.color);
        currentText = design.text;
        currentDesign = design.design;
        currentTextColor = design.textColor;
        currentFontSize = design.fontSize;
        currentSize = design.size;
        currentQuantity = design.quantity;
        
        updateTextDisplay();
        updatePrice();
        
        // Update form elements
        document.getElementById('text-color').value = currentTextColor;
        document.getElementById('font-size').value = currentFontSize;
        document.getElementById('quantity').value = currentQuantity;
        document.querySelector(`input[name="size"][value="${currentSize}"]`).checked = true;
        
        if (currentDesign) {
            document.getElementById('tshirt-design').innerHTML = currentDesign;
        }
    }
}

// Auto-save design every 5 seconds
setInterval(saveDesign, 5000);

// Load saved design on page load
window.addEventListener('load', loadDesign); 
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Care - T-Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f6fc;
            padding-top: 100px;
            color: #333333;
        }
        .contact-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #dddddd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        h2 {
            color: #673ab7;
            margin-bottom: 1.5rem;
        }
        .contact-section {
            margin-bottom: 2rem;
        }
        .contact-section h3 {
            color: #673ab7;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 10px;
            margin-bottom: 1rem;
        }
        .contact-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #555555;
        }
        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #cccccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .contact-form input:focus, .contact-form textarea:focus {
            outline: none;
            border-color: #673ab7;
        }
        .contact-form button {
            background-color: #ffd700;
            color: #000000;
            padding: 10px 20px;
            border: 1px solid #d4af37;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .contact-form button:hover {
            background-color: #e0a800;
        }
        .faq-item {
            margin-bottom: 1rem;
        }
        .faq-item h4 {
            margin-bottom: 0.5rem;
            color: #673ab7;
        }
        .contact-section p a {
            color: #673ab7;
            font-weight: bold;
        }
        .contact-section p a:hover {
            text-decoration: underline;
        }
        .form-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        .form-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .form-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <main>
        <div class="contact-container">
            <h2>Customer Care</h2>

            <?php
            if (isset($_SESSION['contact_message'])) {
                $message = $_SESSION['contact_message'];
                $type = $_SESSION['contact_message_type'];
                echo "<div class='form-message $type'>$message</div>";
                unset($_SESSION['contact_message']);
                unset($_SESSION['contact_message_type']);
            }
            ?>

            <div class="contact-section">
                <h3>Send us a Message</h3>
                <form class="contact-form" action="../backend/contact_form_handler.php" method="post">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="email">Your Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                    
                    <button type="submit">Send Message</button>
                </form>
            </div>
            
            <div class="contact-section">
                <h3>Contact Information</h3>
                <p><strong>Email:</strong> <a href="mailto:support@tstore.com">support@tstore.com</a></p>
                <p><strong>Phone:</strong> 071234567</p>
                <p><strong>Address:</strong> 90,Gangodawila Road,Mahara,Hambanthota.</p>
            </div>

            <div class="contact-section">
                <h3>Frequently Asked Questions</h3>
                <div class="faq-item">
                    <h4>How do I track my order?</h4>
                    <p>You can track your order status from your profile's order history page or by using the tracking number provided in your shipping confirmation email.</p>
                </div>
                <div class="faq-item">
                    <h4>What is your return policy?</h4>
                    <p>We accept returns within 30 days of purchase for a full refund, provided the items are in their original condition. Please visit our returns page for more details.</p>
                </div>
                <div class="faq-item">
                    <h4>How do I customize a T-shirt?</h4>
                    <p>Navigate to our "Customize" page, where you can choose your T-shirt, upload your design, and add text. Our design tool is easy to use and provides a live preview of your creation.</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 T-Store. All rights reserved.</p>
        </div>
    </footer>

</body>
</html> 
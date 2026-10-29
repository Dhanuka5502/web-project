<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_message'] = "Invalid input provided. Please fill all fields correctly.";
        $_SESSION['contact_message_type'] = "error";
        header("location: ../frontend/contact.php");
        exit;
    }

    // Email details
    $to = "support@tstore.com"; // Your support email address
    $subject = "New Contact Form Submission from " . $name;
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Email body
    $email_body = "You have received a new message from your website contact form.\n\n";
    $email_body .= "Here are the details:\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Message:\n$message\n";

    // Send the email
    // if (mail($to, $subject, $email_body, $headers)) {
    //     $_SESSION['contact_message'] = "Thank you for contacting us! We will get back to you shortly.";
    //     $_SESSION['contact_message_type'] = "success";
    // } else {
    //     $_SESSION['contact_message'] = "Sorry, there was an error sending your message. Please try again later.";
    //     $_SESSION['contact_message_type'] = "error";
    // }
    
    // For local development, we'll simulate a success
    $_SESSION['contact_message'] = "Thank you for contacting us! We will get back to you shortly. (Simulated success)";
    $_SESSION['contact_message_type'] = "success";

} else {
    // Not a POST request
    $_SESSION['contact_message'] = "Invalid request method.";
    $_SESSION['contact_message_type'] = "error";
}

header("location: ../frontend/contact.php");
exit;
?> 
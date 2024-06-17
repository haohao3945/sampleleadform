<?php
require 'config.php';

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Validate phone number (basic check)
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        die("Invalid phone number format");
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO leads (name, email, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $phone);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Send email to client
    $to = "client@example.com"; // Client's email address
    $subject = "New Lead Submission";
    $message = "A new lead has been submitted.\n\nName: $name\nEmail: $email\nPhone: $phone";
    $headers = "From: noreply@yourdomain.com";

    mail($to, $subject, $message, $headers);

    header("Location: success.html");
    exit();
}
?>

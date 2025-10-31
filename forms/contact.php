<?php
// Simple contact form handler
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic form handling
function get_post($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$name    = filter_var(get_post('name'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email   = filter_var(get_post('email'), FILTER_VALIDATE_EMAIL);
$subject = filter_var(get_post('subject'), FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'New message from website';
$message = filter_var(get_post('message'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$phone   = filter_var(get_post('phone'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Validation
if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please provide name, a valid email and a message.']);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ngongochris415@gmail.com';
    $mail->Password = 'rgwl gobw cepu uihg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('ngongochris415@gmail.com', 'Website Contact');
    $mail->addAddress('ngongochris415@gmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body = "<h3>New contact form submission</h3>" .
                  "<p><strong>Name:</strong> " . htmlspecialchars($_POST['name']) . "</p>" .
                  "<p><strong>Email:</strong> " . htmlspecialchars($_POST['email']) . "</p>" .
                  "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($_POST['message'])) . "</p>";

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully.']);
} catch (Exception $e) {
    error_log("Mailer Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Please try again later."]);
}

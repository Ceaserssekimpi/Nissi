<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

/* Anti-spam honeypot */
if (!empty($_POST['website'])) {
    header("Location: index.html");
    exit;
}

/* Get form data */
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

/* Validate */
if ($name === '' || $email === '' || $service === '' || $message === '') {
    die("Please complete all required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

/* Create PHPMailer */
$mail = new PHPMailer(true);

try {

    /* SMTP configuration */
    $mail->isSMTP();

    /*
     * IMPORTANT:
     * Replace these with the SMTP details of
     * the company email provider for nissitech.co.ug
     */

    $mail->Host       = 'smtp.example.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@nissitech.co.ug';
    $mail->Password   = 'YOUR_EMAIL_PASSWORD';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    /* Sender */
    $mail->setFrom(
        'info@nissitech.co.ug',
        'Nissi Technologies Website'
    );

    /* Recipient */
    $mail->addAddress(
        'info@nissitech.co.ug',
        'Nissi Technologies'
    );

    /* Allow reply directly to customer */
    $mail->addReplyTo($email, $name);

    /* Email format */
    $mail->isHTML(true);

    $mail->Subject = 'New Website Enquiry - Nissi Technologies';

    $mail->Body = '
    <div style="font-family:Arial,sans-serif;line-height:1.6;color:#333">

        <h2 style="color:#0b63ce;">
            New Website Enquiry
        </h2>

        <p>
            A new enquiry has been submitted through the
            Nissi Technologies website.
        </p>

        <hr>

        <p><strong>Name:</strong><br>'
        . htmlspecialchars($name) .
        '</p>

        <p><strong>Email:</strong><br>'
        . htmlspecialchars($email) .
        '</p>

        <p><strong>Phone:</strong><br>'
        . htmlspecialchars($phone) .
        '</p>

        <p><strong>Service Required:</strong><br>'
        . htmlspecialchars($service) .
        '</p>

        <p><strong>Requirement:</strong><br>'
        . nl2br(htmlspecialchars($message)) .
        '</p>

        <hr>

        <p>
            <strong>Submitted from:</strong>
            Nissi Technologies Website
        </p>

    </div>';

    /* Plain text version */
    $mail->AltBody =
        "NEW WEBSITE ENQUIRY\n\n" .
        "Name: $name\n" .
        "Email: $email\n" .
        "Phone: $phone\n" .
        "Service: $service\n\n" .
        "Requirement:\n$message";

    /* Send enquiry */
    $mail->send();

    /*
     * Confirmation email to customer
     */

    $confirmation = new PHPMailer(true);

    $confirmation->isSMTP();
    $confirmation->Host       = 'smtp.example.com';
    $confirmation->SMTPAuth   = true;
    $confirmation->Username   = 'info@nissitech.co.ug';
    $confirmation->Password   = 'YOUR_EMAIL_PASSWORD';
    $confirmation->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $confirmation->Port       = 587;

    $confirmation->setFrom(
        'info@nissitech.co.ug',
        'Nissi Technologies'
    );

    $confirmation->addAddress($email, $name);

    $confirmation->isHTML(true);

    $confirmation->Subject =
        'We received your enquiry - Nissi Technologies';

    $confirmation->Body = '
    <div style="font-family:Arial,sans-serif;line-height:1.7;color:#333">

        <h2 style="color:#0b63ce;">
            Thank You for Contacting Nissi Technologies
        </h2>

        <p>Dear ' . htmlspecialchars($name) . ',</p>

        <p>
            Thank you for contacting
            <strong>Nissi Technologies</strong>.
        </p>

        <p>
            We have successfully received your enquiry regarding:
        </p>

        <p>
            <strong>' . htmlspecialchars($service) . '</strong>
        </p>

        <p>
            Our team will review your request and get back to you
            as soon as possible.
        </p>

        <br>

        <p>
            Kind regards,<br>
            <strong>Nissi Technologies</strong>
        </p>

        <p>
            Plot 2 Colville Street,<br>
            Shumuk House, 2nd Floor, Suite 51<br>
            Kampala, Uganda
        </p>

        <p>
            Tel: +256 414 698606<br>
            +256 776 667080<br>
            +256 752 667080
        </p>

        <p>
            Email: info@nissitech.co.ug<br>
            Website: www.nissitech.co.ug
        </p>

    </div>';

    $confirmation->send();

    /* Successful submission */
    header("Location: thank-you.html");
    exit;

} catch (Exception $e) {

    /* Log technical error */
    error_log("Nissi enquiry error: " . $mail->ErrorInfo);

    /* Send user to error page */
    header("Location: enquiry-error.html");
    exit;
}
?>
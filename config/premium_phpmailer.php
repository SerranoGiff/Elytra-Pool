<?php
// composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';  // vendor folder is inside config
require __DIR__ . '/dbcon.php';            // include DB config

// --- CONFIGURATION ---
$smtpHost     = 'smtp.gmail.com';
$smtpPort     = 587;
$smtpUser     = 'contact.elytrapool@gmail.com';
$smtpPassword = 'hipy evja dcip uows'; // your App Password

$fromEmail    = 'no-reply@elytra.io';      // use your own domain
$fromName     = 'Elytra Pool';
$replyToEmail = 'support@elytra.io';
$replyToName  = 'Elytra Support';

// Renewal link base URL
$renewLinkBase = 'https://elytra.io/renew';
$unsubscribeBase = 'https://elytra.io/unsubscribe';
$currentYear = date('Y');

// Create PHPMailer instance
$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtpPort;

    // From and Reply-To addresses
    $mail->setFrom($fromEmail, $fromName);
    $mail->addReplyTo($replyToEmail, $replyToName);

    // Unsubscribe header
    $mail->addCustomHeader('List-Unsubscribe', '<' . $unsubscribeBase . '?email=%recipient%>, <mailto:unsubscribe@elytra.io>');

    // Fetch expired premium users
    $query = "SELECT id, first_name, last_name, email, premium_expiration
              FROM users
             WHERE type = 'premium'
               AND premium_expiration < NOW()";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($user = $result->fetch_assoc()) {
            $toEmail = $user['email'];
            $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            $toName = $fullName ?: $toEmail;

            // Clear addresses and set recipient
            $mail->clearAddresses();
            $mail->addAddress($toEmail, $toName);

            // Personalized links
            $renewLink = $renewLinkBase . '?user=' . urlencode($toEmail);
            $unsubscribeLink = $unsubscribeBase . '?email=' . urlencode($toEmail);

            // Subject
            $mail->Subject = 'Your Premium Subscription Has Expired';

            // HTML body with unsubscribe footer
            $mail->isHTML(true);
            $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Subscription Expired</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
    .header { background: #0073e6; color: #ffffff; padding: 20px; text-align: center; }
    .content { padding: 20px; color: #333333; }
    .button { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #28a745; color: #ffffff; text-decoration: none; border-radius: 4px; }
    .footer { font-size: 12px; color: #777777; padding: 10px 20px; text-align: center; }
    .unsubscribe { font-size: 12px; color: #999999; text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Subscription Expired</h1>
    </div>
    <div class="content">
      <p>Hi {$toName},</p>
      <p>Your <strong>Premium Subscription</strong> expired on <em>{$user['premium_expiration']}</em>. You now have access to Free features only.</p>
      <p>Renew now to continue enjoying all premium benefits, including unlimited access to exclusive content and priority support.</p>
      <p style="text-align:center;"><a class="button" href="{$renewLink}">Renew My Subscription</a></p>
    </div>
    <div class="footer">
      <p>&copy; {$currentYear} Elytra Pool. All rights reserved.</p>
      <p>If you have any questions, reply to this email or contact us at {$replyToEmail}.</p>
      <p class="unsubscribe">If you prefer not to receive these notices, <a href="{$unsubscribeLink}">unsubscribe here</a>.</p>
    </div>
  </div>
</body>
</html>
HTML;

            // Plain-text fallback with unsubscribe
            $mail->AltBody = "Hi {$toName},\n\nYour Premium Subscription expired on {$user['premium_expiration']}.
Renew here: {$renewLink}\n\nTo unsubscribe, visit: {$unsubscribeLink}\n\nThank you,\nElytra Pool";

            // Send
            $mail->send();
            echo "Notification sent to {$toEmail} ({$toName})\n";
        }
    } else {
        echo "No expired premium users found.\n";
    }
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}

<?php
require __DIR__ . '/config.php';
require __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getEmailTemplate($title, $content, $ctaText = null, $ctaUrl = null) {
    global $env;
    $appUrl = $env['APP_URL'] ?? '#';
    
    $bgPage = "#fdf3ea";
    $bgCard = "#ffffff";
    $accent = "#e08aa4";
    $textMain = "#44332b";
    
    $buttonHtml = "";
    if ($ctaText && $ctaUrl) {
        $buttonHtml = "
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$ctaUrl}' style='
                    background-color: {$accent}; 
                    color: #ffffff; 
                    padding: 14px 28px; 
                    text-decoration: none; 
                    border-radius: 50px; 
                    font-weight: bold; 
                    display: inline-block;
                    font-size: 16px;
                '>{$ctaText}</a>
            </div>
        ";
    }

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { margin: 0; padding: 0; font-family: 'Georgia', serif; background-color: {$bgPage}; }
            .container { max-width: 600px; margin: 0 auto; background-color: {$bgPage}; padding: 40px 20px; }
            .card { background-color: {$bgCard}; border-radius: 18px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
            .header { background-color: {$bgCard}; padding: 30px; text-align: center; border-bottom: 2px solid #f9efe9; }
            .header h1 { margin: 0; color: {$textMain}; font-size: 28px; }
            .content { padding: 40px 30px; color: {$textMain}; line-height: 1.6; font-size: 16px; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #998a83; }
            .footer a { color: {$accent}; text-decoration: none; }
        </style>
    </head>
    <body style='margin: 0; padding: 0; font-family: Georgia, serif; background-color: {$bgPage}; color: {$textMain};'>
        <div class='container' style='max-width: 600px; margin: 0 auto; padding: 40px 20px;'>
            <div class='card' style='background-color: {$bgCard}; border-radius: 18px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.05);'>
                <div class='header' style='background-color: {$bgCard}; padding: 30px; text-align: center; border-bottom: 2px solid #f9efe9;'>
                   <h1 style='margin: 0; color: {$textMain}; font-family: Georgia, serif; font-size: 28px;'>Bloomify</h1>
                </div>
                <div class='content' style='padding: 40px 30px; line-height: 1.6;'>
                    <h2 style='margin-top: 0; margin-bottom: 20px; font-size: 22px; color: {$textMain};'>{$title}</h2>
                    {$content}
                    {$buttonHtml}
                    <p style='margin-top: 30px; font-size: 14px; color: #777;'>
                        Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:<br>
                        <a href='{$ctaUrl}' style='color: {$accent}; word-break: break-all;'>{$ctaUrl}</a>
                    </p>
                </div>
            </div>
            <div class='footer' style='text-align: center; padding: 20px; font-size: 12px; color: #998a83;'>
                <p>&copy; " . date('Y') . " Bloomify. All rights reserved.</p>
                <p>Jalan Kaliurang KM 5, Yogyakarta</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

function sendActivationEmail($email, $token)
{
  global $env;

  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host       = $env['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $env['SMTP_USER'];
    $mail->Password   = $env['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $env['SMTP_PORT'];

    $mail->setFrom($env['SMTP_USER'], 'Toko Bunga Bloomify');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Aktivasi Akun Toko Bunga Bloomify (no reply)";

    $activationLink = $env['APP_URL'] . "/activate.php?token=" . urlencode($token);

    $content = "<p>Terima kasih telah mendaftar di Bloomify! Untuk memulai pengalaman berbelanja bunga terbaik, silakan aktifkan akun Anda terlebih dahulu.</p>";
    
    $mail->Body = getEmailTemplate("Selamat Datang di Bloomify!", $content, "Aktifkan Akun Saya", $activationLink);

    return $mail->send();
  } catch (Exception $e) {
    return false;
  }
}

function sendResetPasswordEmail($email, $token)
{
  global $env;

  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host       = $env['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $env['SMTP_USER'];
    $mail->Password   = $env['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $env['SMTP_PORT'];

    $mail->setFrom($env['SMTP_USER'], 'Toko Bunga Bloomify');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Reset Password Toko Bunga Bloomify (no reply)";

    $resetLink = $env['APP_URL'] . "/reset-password.php?token=" . urlencode($token);

    $content = "
        <p>Kami menerima permintaan untuk mereset password akun Anda. Jangan khawatir, akun Anda aman.</p>
        <p>Silakan klik tombol di bawah ini untuk membuat password baru. Link ini hanya berlaku selama 1 jam.</p>
        <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
    ";

    $mail->Body = getEmailTemplate("Reset Password", $content, "Reset Password Saya", $resetLink);

    return $mail->send();
  } catch (Exception $e) {
    return false;
  }
}
?>
<?php
require __DIR__ . '/config.php';
require __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    $mail->SMTPSecure = 'tls';
    $mail->Port       = $env['SMTP_PORT'];

    $mail->setFrom($env['SMTP_USER'], 'Toko Bunga');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Aktivasi Akun Toko Bunga (no reply)";

    $activationLink = $env['APP_URL'] . "/api/auth/activate.php?token=" . urlencode($token);

    $mail->Body = "
            <h3>Aktivasi Akun Anda</h3>
            <p>Silakan klik link berikut untuk mengaktifkan akun Anda:</p>
            <a href='$activationLink'>$activationLink</a>
        ";

    return $mail->send();
  } catch (Exception $e) {
    return false;
  }
}
<?php
// mailer.php — send_email($to, $toName, $subject, $html) → bool
// Priority: PHPMailer (if available) → PHP mail() → no-op (true).

function send_email(string $to, string $toName, string $subject, string $html): bool {
  // Try PHPMailer (Composer or manual include)
  $base = __DIR__;
  $paths = [
    $base . '/vendor/autoload.php',                 // composer
    $base . '/PHPMailer/src/PHPMailer.php'          // manual include
  ];

  foreach ($paths as $p) {
    if (file_exists($p)) {
      if (basename($p) === 'autoload.php') {
        require_once $p;
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
      } else {
        require_once $base . '/PHPMailer/src/PHPMailer.php';
        require_once $base . '/PHPMailer/src/SMTP.php';
        require_once $base . '/PHPMailer/src/Exception.php';
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
      }

      try {
        // TODO: put your SMTP here
        // For Gmail: create an App Password and use smtp.gmail.com:587 TLS
        $mailer->isSMTP();
        $mailer->Host       = 'smtp.gmail.com';
        $mailer->Port       = 587;
        $mailer->SMTPAuth   = true;
        $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->Username   = 'nitinravipal@gmail.com';
        $mailer->Password   = 'bexcyqmjjivbofuz';

        $mailer->setFrom('nitinravipal@gmail.com', 'Asset Management');
        $mailer->addAddress($to, $toName ?: $to);
        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $html;

        $mailer->send();
        return true;
      } catch (\Throwable $e) {
        // fall through to mail()
      }
    }
  }

  // Fallback: PHP mail() (often disabled on Windows). Try anyway.
  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8\r\n";
  $headers .= "From: Asset Management <nitinravipal@gmail.com>\r\n";
  return @mail($to, $subject, $html, $headers) ? true : false;
}


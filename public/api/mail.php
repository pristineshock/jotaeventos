<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Origin: https://www.jotaeventos.com.ar");

if (isset($_ENV["MAIL_HOST"])) {
  require __DIR__ . "/config/config.prod.php";
} else {
  require __DIR__ . "/config/config.local.php";
}

require __DIR__ . "/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use ReCaptcha\ReCaptcha;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $recaptcha = new ReCaptcha(GOOGLE_CAPTCHA_PRIVATE);

  $return = null;

  // Post Name Sanitation and Validation
  if (isset($_REQUEST["name"])) {
    $name = htmlspecialchars($_REQUEST["name"]);

    if (strlen($name) <= 2) {
      $return["nameErr"] = "El nombre debe de ser de tres letras minino.";
    }
  } else {
    $return["nameErr"] = "Por favor, completa este campo.";
  }

  // Post Mail Sanitation and Validation
  if (isset($_REQUEST["mail"])) {
    $mail = htmlspecialchars($_REQUEST["mail"]);

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $return["mailErr"] = "$mail no es una dirección de email valida.";
    }
  } else {
    $return["mailErr"] = "Por favor, completa este campo.";
  }

  // Post Message Sanitation and Validation
  if (isset($_REQUEST["message"])) {
    $message = htmlspecialchars($_REQUEST["message"]);

    if (strlen($message) <= 20) {
      $return["messageErr"] = "El mensaje debe de ser de al menos 20 caracteres.";
    }
  } else {
    $return["messageErr"] = "Por favor, completa este campo.";
  }

  // Captcha Sanitation and Validation
  if (isset($_REQUEST["recaptcha"])) {
    $captcha = htmlspecialchars($_REQUEST["recaptcha"]);

    $resp = $recaptcha->setExpectedHostname($_SERVER["SERVER_NAME"])->verify($captcha, $_SERVER["REMOTE_ADDR"]);
    if ($resp->isSuccess()) {
      // Verified!
    } else {
      $return["captchaErr"] = $resp->getErrorCodes();
    }
  } else {
    $return["captchaErr"] = "Por favor, complete el captcha.";
  }

  // Check if there are errors
  if (!empty($return["nameErr"]) || !empty($return["mailErr"]) || !empty($return["messageErr"]) || !empty($return["captchaErr"])) {
    $return["status"] = "failed";
    $return["message"] = "Por favor, corrija los errores.";
    echo json_encode($return);
    return;
  }

  // Try to send the mail
  $mailer = new PHPMailer(true);

  try {
    $mailer->isSMTP();
    $mailer->Host = MAIL_HOST;
    $mailer->SMTPAuth = true;
    $mailer->Username = MAIL_USER;
    $mailer->Password = MAIL_PASS;
    $mailer->SMTPSecure = "tls";
    $mailer->Port = MAIL_PORT;

    $mailer->setFrom(MAIL_FROM, "PristineShock");
    $mailer->addAddress(MAIL_TO, "JotaEventos");
    $mailer->addReplyTo(MAIL_FROM, "PristineShock");

    $mailer->isHTML(true);
    $mailer->Subject = "There is a new message in JotaEventos.";
    $mailer->Body = "There is a new message in JotaEventos.<br /><br />Message from: " . $name . " ( " . $mail . " ) <br /><br />" . $message;
    $mailer->AltBody = "There is a new message in JotaEventos./n/nMessage from: " . $name . " ( " . $mail . " ) /n/n" . $message;

    $mailer->send();
    $return["status"] = "success";
    $return["message"] = "Gracias por enviar este mensaje. Le enviaremos un correo electrónico lo antes posible.";
    echo json_encode($return);
  } catch (Exception $e) {
    $return["status"] = "failed";
    $return["message"] = "Hubo un error al enviar este mensaje. Por favor, inténtelo de nuevo más tarde o contacte al administrador en " . MAIL_FROM;
    echo json_encode($return);
  }
} else {
  // Redirect if not a POST method
  header("Location: /");
  die();
}

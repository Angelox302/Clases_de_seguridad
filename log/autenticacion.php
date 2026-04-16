<?php
session_start();

// Validar CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Error: Token CSRF inválido o la sesión ha expirado.');
}

// Validar reCAPTCHA
$recaptcha_secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // Llave secreta mock de pruebas de Google
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

// Realizar petición a la API de Google para confirmar
$verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
$response_data = json_decode($verify_response);

if (!$response_data->success) {
    // Si el captcha falla o expiró, regresar con error
    echo "<script>alert('Error: reCAPTCHA inválido. Intenta de nuevo.'); window.location.href='index.php';</script>";
    exit();
}

// Se valida si se ha enviado información
if (!isset($_POST['username'], $_POST['password'])) {
    header('Location: index.php');
    exit();
}

// Login Local Simulando DB
// En este escenario sin BD, si el usuario y la contraseña cumplen los requisitos desde el Frontend y captcha de Google pasaron, se acepta la solicitud
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    session_regenerate_id();
    $_SESSION['loggedin'] = TRUE;
    $_SESSION['name'] = $_POST['username'];
    $_SESSION['last_activity'] = time(); // Guardar tiempo exacto para cerrar por inactividad
    header('Location: inicio.php');
} else {
    header('Location: index.php');
}
?>

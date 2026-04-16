<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    // Generar un token CSRF y guardarlo en la sesión
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mostrar mensaje de timeout si existe
$timeout_msg = "";
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_msg = "<p style='text-align:center; color:red;'>Tu sesión ha expirado por inactividad.</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="login">
        <h1>Sistema de Login Básico ConfiguroWeb</h1>
        
        <?php echo $timeout_msg; ?>

        <form action="autenticacion.php" method="post" id="loginForm">
            <!-- Campo oculto para el token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <label for="username">
                <i class="fas fa-user"></i>
            </label>
            <input type="text" name="username"
            placeholder="Usuario (Ej. jperez)" id="username" required pattern="^[^@\s]+$" title="Ingrese su usuario usando la inicial de su nombre y su apellido. No se aceptan correos ni espacios.">
            
            <label for="password">
                <i class="fas fa-lock"></i>
            </label>
            <input type="password" name="password"
            placeholder="Contraseña" id="password" required maxlength="8">
            
            <div id="password-validation" class="validation-container">
                <p>Requisitos de la contraseña:</p>
                <ul class="req-list">
                    <li id="req-length" class="invalid"><i class="fas fa-times"></i> Máximo 8 caracteres</li>
                    <li id="req-number" class="invalid"><i class="fas fa-times"></i> Al menos 1 número</li>
                    <li id="req-special" class="invalid"><i class="fas fa-times"></i> Al menos 1 carácter especial</li>
                    <li id="req-upper" class="invalid"><i class="fas fa-times"></i> Al menos 1 mayúscula</li>
                    <li id="req-nospace" class="invalid"><i class="fas fa-times"></i> Sin espacios vacíos</li>
                </ul>
            </div>

            <!-- Widget de reCAPTCHA con llaves de testing de Google -->
            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin-bottom: 20px; display: flex; justify-content: center;"></div>

            <input type="submit" value="Acceder">
        </form>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const form = document.getElementById('loginForm');
            
            const reqLength = document.getElementById('req-length');
            const reqNumber = document.getElementById('req-number');
            const reqSpecial = document.getElementById('req-special');
            const reqUpper = document.getElementById('req-upper');
            const reqNoSpace = document.getElementById('req-nospace');

            function updateRequirement(element, isValid) {
                const icon = element.querySelector('i');
                if (isValid) {
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-check');
                } else {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-times');
                }
            }

            passwordInput.addEventListener('input', function() {
                const val = passwordInput.value;
                
                updateRequirement(reqLength, val.length > 0 && val.length <= 8);
                updateRequirement(reqNumber, /\d/.test(val));
                updateRequirement(reqSpecial, /[^A-Za-z0-9\s]/.test(val));
                updateRequirement(reqUpper, /[A-Z]/.test(val));
                updateRequirement(reqNoSpace, val.length > 0 && !/\s/.test(val));
            });

            form.addEventListener('submit', function(e) {
                const val = passwordInput.value;
                const isValid = val.length > 0 && val.length <= 8 && 
                                /\d/.test(val) && 
                                /[^A-Za-z0-9\s]/.test(val) && 
                                /[A-Z]/.test(val) && 
                                !/\s/.test(val);
                                
                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor, cumpla con todos los requisitos de la contraseña.');
                    return;
                }

                // Validación manual de reCAPTCHA antes de enviar
                var recaptchaResponse = document.getElementById('g-recaptcha-response').value;
                if(recaptchaResponse.length === 0) {
                    e.preventDefault();
                    alert("Por favor, completa el captcha de Google.");
                }
            });
        });
    </script>
</body>
</html>

<?php
// confirmar sesion
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

$timeout_duration = 60; // 1 minuto
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();     
    session_destroy();   
    header("Location: index.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1>Sistema de Login Básico ConfiguroWeb</h1>
            <a href="perfil.php"><i class="fas fa-user-circle"></i>Información de Usuario</a>
            <a href="cerrar-sesion.php"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a>
        </div>
    </nav>

    <div class="content">
        <h2>Dashboard Principal</h2>
        <p>Hola, <strong><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></strong> !!! Has iniciado sesión correctamente.</p>
        <div style="background-color: #f8f9fa; color: #495057; padding:15px; border-radius: 8px; margin-top:20px; border: 1px solid #dee2e6; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-clock" style="color: #6f42c1;"></i> 
            <span>Tu sesión expirará en: <strong id="session-timer">1:00</strong></span>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeoutDuration = <?= $timeout_duration ?>;
            let timeLeft = timeoutDuration;
            const timerDisplay = document.getElementById('session-timer');
            let lastHeartbeat = Date.now();

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (timeLeft <= 0) {
                    window.location.href = 'cerrar-sesion.php?timeout=1';
                }
                timeLeft--;
            }

            function resetTimer() {
                timeLeft = timeoutDuration;
                // Enviar un "latido" al servidor máximo cada 30 segundos para no saturar
                if (Date.now() - lastHeartbeat > 30000) {
                    fetch('heartbeat.php')
                        .then(response => response.text())
                        .then(data => {
                            if (data === 'ok') {
                                lastHeartbeat = Date.now();
                            } else if (data === 'expired') {
                                window.location.href = 'cerrar-sesion.php?timeout=1';
                            }
                        });
                }
            }

            // Detectar actividad del usuario
            ['mousemove', 'mousedown', 'keypress', 'touchstart', 'scroll'].forEach(event => {
                document.addEventListener(event, resetTimer, { passive: true });
            });

            setInterval(updateTimer, 1000);
            updateTimer();
        });
    </script>
</body>

</html>
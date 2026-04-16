<?php
session_start();
if (isset($_SESSION['loggedin'])) {
    $_SESSION['last_activity'] = time();
    echo "ok";
} else {
    echo "expired";
}

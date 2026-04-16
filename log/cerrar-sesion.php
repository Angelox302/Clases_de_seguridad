<?php
session_start();
session_destroy();

$extra = isset($_GET['timeout']) ? '?timeout=1' : '';
header('Location: index.php' . $extra);
exit;

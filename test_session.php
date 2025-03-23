<?php
session_start();

if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = "A sessão está funcionando!";
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

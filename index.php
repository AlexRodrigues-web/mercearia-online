<?php
session_start();
define("MERCEARIA2021", true); //altere o nome dessa constante para um nome mais difícil.
date_default_timezone_set('Europe/Lisbon'); // Alterado para o fuso horário de Porto, Portugal

require_once './vendor/autoload.php';
$url = new Core\ConfigController();
$url->carregar();
?>

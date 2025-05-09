<?php
namespace App\Controller;

if (!defined("MERCEARIA2025")) {
    die("Acesso negado.");
}

class Institucional
{
    public function index()
    {
        require_once 'app/View/institucional/index.php';
    }
}

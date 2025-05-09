<?php
namespace App\Controller;

use Core\ConfigView;

class Manutencao extends Controller
{
    public function index(): void
    {
        $view = new ConfigView("manutencao/em_manutencao");
        $view->renderizar();
    }
}

<?php
namespace App\Controller;

use Core\ConfigView;

class Paginas extends Controller
{
    public function index(): void
    {
        $dados = [
            'titulo' => 'Páginas Institucionais',
            'subtitulo' => 'Gerencie aqui as páginas públicas do site (ex: Privacidade, Termos, Entregas)',
        ];

        $carregarView = new ConfigView("paginas/index", $dados);
        $carregarView->renderizar();
    }
}

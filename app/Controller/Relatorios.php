<?php
namespace App\Controller;

use Core\ConfigView;

class Relatorios extends Controller
{
    public function index(): void
    {
        $dados = [
            'titulo' => 'Relatórios',
            'subtitulo' => 'Visão Geral de Vendas, Produtos e Usuários',
        ];

        $carregarView = new ConfigView("relatorios/index", $dados);
        $carregarView->renderizar();
    }
}

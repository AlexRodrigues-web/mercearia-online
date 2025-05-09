<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigController;
use Core\ConfigView;
use App\Model\HomeModel;

class Home extends Controller
{
    private HomeModel $homeModel;
    private array $dados = [];

    public function __construct(HomeModel $homeModel = null)
    {
        parent::__construct();
        $this->homeModel = $homeModel ?? new HomeModel();
        
        // Permite acesso público à página inicial e restringe apenas páginas específicas
        if ($this->rotaEhRestrita()) {
            if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
                $_SESSION['msg_erro'] = "Você precisa estar logado para acessar essa página.";
                $this->forcarLogout();
                exit();
            }

            // 🔍 Verifica se as permissões do usuário estão carregadas corretamente
            if (!isset($_SESSION['usuario_paginas']) || empty($_SESSION['usuario_paginas'])) {
                $_SESSION['msg_erro'] = "Erro ao carregar permissões. Faça login novamente.";
                $this->forcarLogout();
                exit();
            }
        }
    }

    public function index()
    {
        try {
            // 🔹 Obtém **todos** os produtos para o carrossel
            $dadosIniciais = $this->homeModel->obterTodosOsProdutos(); // 🔄 ALTERADO PARA PEGAR TODOS OS PRODUTOS
            $this->dados = is_array($dadosIniciais) ? $this->sanitizarDados($dadosIniciais) : [];

            // 🔹 Inclui permissões do usuário nos dados da view
            $dadosView = [
                'dados' => $this->dados,
                'permissoes' => $_SESSION['usuario_paginas'] ?? []
            ];

            // 🔹 Renderiza a view corretamente
            $view = new ConfigView("home/index", $dadosView);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao carregar a página inicial: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar a página inicial.";
            $this->redirecionar("erro");
            exit();
        }
    }

    private function sanitizarDados(array $dados): array
    {
        return array_map(function ($item) {
            return is_string($item) ? htmlspecialchars($item, ENT_QUOTES, 'UTF-8') : $item;
        }, $dados);
    }

    private function forcarLogout(): void
    {
        session_destroy();
        header("Location: " . BASE_URL . "login");
        exit();
    }

    private function rotaEhRestrita(): bool
    {
        return in_array(strtolower($_GET['url'] ?? ''), ['admin', 'painel', 'configuracoes']);
    }
}

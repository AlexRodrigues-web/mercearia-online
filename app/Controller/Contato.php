<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Contato extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // 🔒 Verifica autenticação do usuário
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar essa página.";
            $this->redirecionar("login");
            return;
        }
    }

    public function index()
    {
        try {
            $viewPath = "contato/index";

            // ✅ Verifica se a view existe antes de renderizar
            if (!file_exists(__DIR__ . "/../View/{$viewPath}.php")) {
                throw new \Exception("A view {$viewPath} não foi encontrada.");
            }

            $view = new ConfigView($viewPath);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao carregar a página Contato: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar a página Contato.";
            $this->redirecionar("erro");
        }
    }
}

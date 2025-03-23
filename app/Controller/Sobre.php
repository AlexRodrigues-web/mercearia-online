<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Sobre extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // 🔒 Verifica autenticação do usuário antes de acessar a página
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar essa página.";
            $this->redirecionar("login");
            exit();
        }
    }

    public function index()
    {
        try {
            $viewPath = "sobre/index";

            // ✅ Verifica se a view existe antes de renderizar, protegendo contra Path Traversal
            $caminhoView = realpath(__DIR__ . "/../../View/{$viewPath}.php");

            if (!$caminhoView || !file_exists($caminhoView)) {
                throw new \Exception("A view '{$viewPath}' não foi encontrada.");
            }

            $view = new ConfigView($viewPath);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao carregar a página Sobre: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar a página Sobre.";
            $this->redirecionar("erro");
        }
    }

    private function redirecionar(string $rota): void
    {
        $baseUrl = defined("BASE_URL") ? BASE_URL : "/";

        // ✅ Evita erro de headers já enviados
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Location: " . $baseUrl . trim($rota, '/'));
        exit();
    }
}

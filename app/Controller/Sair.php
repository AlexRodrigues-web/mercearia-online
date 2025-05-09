<?php 
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Controller; 

class Sair extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            // Inicia a sessão se ainda não estiver ativa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Verifica se a sessão está ativa antes de destruí-la
            if (session_status() === PHP_SESSION_ACTIVE) {
                // Preserva carrinho e desconto antes de destruir a sessão
                $carrinhoTemp = $_SESSION['carrinho'] ?? [];
                $descontoTemp = $_SESSION['desconto'] ?? 0;

                $_SESSION = [];

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(
                        session_name(),
                        '',
                        time() - 3600,
                        $params["path"],
                        $params["domain"],
                        $params["secure"],
                        $params["httponly"]
                    );
                }

                session_destroy();

                // Recria sessão e restaura carrinho
                session_start();
                $_SESSION['usuario'] = [
                    'logado' => false,
                    'nivel_nome' => 'visitante',
                    'paginas' => []
                ];
                $_SESSION['carrinho'] = $carrinhoTemp;
                $_SESSION['desconto'] = $descontoTemp;
            }

            $baseUrl = defined("BASE_URL") ? BASE_URL : "/";

            if (ob_get_length()) {
                ob_end_clean();
            }

            header("Location: " . $baseUrl . "login");
            exit;
        } catch (\Exception $e) {
            error_log("Erro ao realizar logout: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao encerrar a sessão.";

            if (ob_get_length()) {
                ob_end_clean();
            }

            header("Location: " . (defined("BASE_URL") ? BASE_URL : "/") . "erro");
            exit;
        }
    }
}

<?php 
declare(strict_types=1);

namespace App\Controller;

class Sair extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            // ✅ Inicia a sessão se ainda não estiver ativa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // ✅ Verifica se a sessão está ativa antes de destruí-la
            if (session_status() === PHP_SESSION_ACTIVE) {
                // ✅ Limpa todas as variáveis da sessão
                $_SESSION = [];

                // ✅ Remove o cookie de sessão, se configurado
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

                // ✅ Destrói a sessão corretamente
                session_destroy();
            }

            // ✅ Garante que `BASE_URL` está definida antes de usar
            $baseUrl = defined("BASE_URL") ? BASE_URL : "/";

            // ✅ Evita erro de headers já enviados
            if (ob_get_length()) {
                ob_end_clean();
            }

            // ✅ Redireciona para a página de login após o logout
            header("Location: " . $baseUrl . "login");
            exit;
        } catch (\Exception $e) {
            error_log("Erro ao realizar logout: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao encerrar a sessão.";

            // ✅ Evita erro de headers já enviados
            if (ob_get_length()) {
                ob_end_clean();
            }

            header("Location: " . (defined("BASE_URL") ? BASE_URL : "/") . "erro");
            exit;
        }
    }
}

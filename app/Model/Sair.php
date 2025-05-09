<?php

namespace App\Model;

class Sair
{
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Registra a saída do usuário no log do sistema
        $this->registrarLogout();

        // Remove todas as variáveis da sessão
        $_SESSION = [];

        // Destrói a sessão e remove cookies
        $this->destroySession();

        // Redireciona para a página de login de forma segura
        header("Location: " . $this->getBaseUrl() . "/login");
        exit();
    }

    /**
     * Destroi completamente a sessão e remove o cookie do navegador.
     */
    private function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
            session_write_close();
        }

        $this->removerCookies();

        // Regenera um novo ID de sessão para evitar reutilização de sessão antiga
        session_start();
        session_regenerate_id(true);
    }

    /**
     * Remove cookies de sessão do navegador para garantir logout total.
     */
    private function removerCookies(): void
    {
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
    }

    /**
     * Registra o logout no log do sistema.
     */
    private function registrarLogout(): void
    {
        if (!isset($_SESSION['usuario_nome'])) {
            $_SESSION['usuario_nome'] = 'Usuário Desconhecido';
        }

        $mensagem = sprintf(
            "[%s] Logout: Usuário '%s' (IP: %s, Sessão: %s) saiu do sistema.",
            date('Y-m-d H:i:s'),
            $_SESSION['usuario_nome'],
            $_SERVER['REMOTE_ADDR'] ?? 'IP Desconhecido',
            session_id()
        );

        error_log($mensagem);
    }

    /**
     * Retorna a URL base da aplicação de forma segura.
     *
     * @return string URL base do sistema
     */
    private function getBaseUrl(): string
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return rtrim($protocolo . "://" . $host, '/');
    }
}

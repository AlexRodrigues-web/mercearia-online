<?php

namespace App\Model;

class SairModel
{
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->registrarLogout();

        $_SESSION = [];

        $this->destroySession();

        header("Location: " . $this->getBaseUrl() . "/login");
        exit();
    }

    private function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
            session_write_close();
        }

        $this->removerCookies();

        session_start();
        session_regenerate_id(true);
    }

    private function removerCookies(): void
    {
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
    }

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

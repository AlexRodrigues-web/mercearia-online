<?php
declare(strict_types=1);

namespace App\Controller;
use Core\ConfigController;  
use Core\ConfigView;

class PaginaInvalida extends Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        parent::__construct();
    }


    public function index(): void
    {
        try {
            if (method_exists($this, 'alertaFalha')) {
                $_SESSION['msg_erro'][] = $this->alertaFalha(
                    "A página que você tentou acessar é inválida ou não existe."
                );
            } else {
                error_log("Método 'alertaFalha' não encontrado na classe pai.");
            }

            if (!$this->viewExiste('erro/paginaInvalida')) {
                throw new \Exception("Arquivo da view 'paginaInvalida' não encontrado.");
            }

            $view = new ConfigView('erro/paginaInvalida');
            $view->renderizar();

        } catch (\Exception $e) {
            error_log("Erro ao processar página inválida: " . $e->getMessage());

            if (method_exists($this, 'alertaFalha')) {
                $_SESSION['msg_erro'][] = $this->alertaFalha(
                    "Erro inesperado ao carregar a página inválida. Tente novamente."
                );
            }

            $this->redirecionar("erro");
        }
    }

    /**
     * Obtém a URL base do sistema de forma dinâmica e segura.
     *
     * @return string Retorna a base URL validada
     */
    private function getBaseUrl(): string
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = $_SERVER['REQUEST_URI'] ?? '';

        $scriptDir = preg_replace('/[^a-zA-Z0-9\/_\-]/', '', trim(dirname($scriptDir), '/'));

        return "{$protocolo}://{$host}/" . ($scriptDir ? "{$scriptDir}/" : "");
    }

    /**
     * Verifica se a view existe antes de renderizar.
     *
     * @param string $view Caminho relativo da view
     * @return bool Retorna true se a view existir, false caso contrário
     */
    private function viewExiste(string $view): bool
    {
        $caminhoView = realpath(__DIR__ . "/../../View/" . str_replace('.', '/', $view) . ".php");

        if ($caminhoView === false || !file_exists($caminhoView)) {
            error_log("View não encontrada: {$view}");
            return false;
        }

        if (strpos($caminhoView, realpath(__DIR__ . "/../../View")) !== 0) {
            error_log("Tentativa de Path Traversal detectada ao carregar a view: {$view}");
            return false;
        }

        return true;
    }

    /**
     * Redireciona o usuário para uma determinada rota.
     *
     * @param string $rota Caminho relativo da rota
     */
    private function redirecionar(string $rota): void
    {
        $urlDestino = $this->getBaseUrl() . trim($rota, '/');

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL) ||
            parse_url($urlDestino, PHP_URL_HOST) !== parse_url($this->getBaseUrl(), PHP_URL_HOST)) {
            error_log("Tentativa de redirecionamento para URL inválida: {$urlDestino}");
            if (method_exists($this, 'alertaFalha')) {
                $_SESSION['msg_erro'][] = $this->alertaFalha("Erro ao redirecionar.");
            }
            exit();
        }

        header("Location: {$urlDestino}", true, 302);
        exit();
    }
}

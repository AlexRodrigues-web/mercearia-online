<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\PaginaPublicaModel;
use Core\ConfigView;

class PaginaPublica extends Controller
{
    private PaginaPublicaModel $paginaPublica;
    private array $dados = [];

    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->paginaPublica = new PaginaPublicaModel();

        if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        if (!in_array($_SESSION['usuario']['nivel_nome'], ['admin', 'funcionario'], true)) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores e funcionários podem acessar essa área.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index(): void
    {
        try {
            $this->dados = $this->paginaPublica->listar() ?? [];

            if (empty($this->dados)) {
                $_SESSION['msg_erro'] = "Nenhuma página pública encontrada.";
            }

            $view = new ConfigView('pagina_publica/index', ['paginas' => $this->dados]);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao listar páginas públicas: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao listar páginas públicas.";
            $this->redirecionar("erro");
        }
    }

    public function atualizar(): void
    {
        $this->validarCsrfToken();

        $this->dados = filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'titulo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'conteudo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];

        if (!$this->validarDados($this->dados)) {
            $_SESSION['msg_erro'] = "Dados inválidos ou incompletos.";
            $this->redirecionar("pagina_publica/editar?id=" . ($this->dados['id'] ?? ''));
        }

        try {
            $resultado = $this->paginaPublica->atualizar($this->dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Página pública atualizada com sucesso."
                : "Erro ao atualizar a página pública. Tente novamente.";

            $this->redirecionar("pagina_publica/index");
        } catch (\Exception $e) {
            error_log("Erro ao atualizar página pública: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao atualizar a página.";
            $this->redirecionar("erro");
        }
    }

    private function validarDados(array $dados): bool
    {
        if (empty($dados['id']) || !is_numeric($dados['id'])) {
            return false;
        }

        if (empty(trim($dados['titulo'])) || empty(trim($dados['conteudo']))) {
            return false;
        }

        return true;
    }

    private function redirecionar(string $rota): void
    {
        $urlDestino = defined("BASE_URL") ? BASE_URL . trim($rota, '/') : '/' . trim($rota, '/');

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("URL de redirecionamento inválida: {$urlDestino}");
            $_SESSION['msg_erro'] = "Erro ao redirecionar.";
            exit();
        }

        header("Location: " . $urlDestino);
        exit();
    }

    private function validarCsrfToken(): void
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Requisição inválida (CSRF).";
            error_log("Tentativa de CSRF detectada.");
            $this->gerarCsrfToken();
            $this->redirecionar("pagina_publica");
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

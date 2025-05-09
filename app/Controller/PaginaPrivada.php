<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\PaginaPrivadaModel;
use Core\ConfigView;

class PaginaPrivada extends Controller
{
    private PaginaPrivadaModel $paginaPrivada;
    private array $dados = [];

    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->paginaPrivada = new PaginaPrivadaModel();

        if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        if (!in_array($_SESSION['usuario']['nivel_nome'], ['admin', 'funcionario'], true)) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores e funcionários podem acessar essa página.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index(): void
    {
        $this->listar();

        if (!$this->viewExiste('pagina_privada/index')) {
            error_log("Erro: View 'pagina_privada/index' não encontrada.");
            $_SESSION['msg_erro'] = "Erro ao carregar a página.";
            $this->redirecionar("erro");
            exit();
        }

        $view = new ConfigView('pagina_privada/index', ['paginas' => $this->dados]);
        $view->renderizar();
    }

    private function listar(): void
    {
        try {
            $this->dados = $this->paginaPrivada->listar() ?? [];

            if (empty($this->dados)) {
                $_SESSION['msg_erro'] = "Nenhuma página privada encontrada.";
            }
        } catch (\Exception $e) {
            error_log("Erro ao listar páginas privadas: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar as páginas.";
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
            $this->redirecionar("pagina_privada/editar?id=" . ($this->dados['id'] ?? ''));
            return;
        }

        try {
            $resultado = $this->paginaPrivada->atualizar($this->dados);
            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Página privada atualizada com sucesso."
                : "Erro ao atualizar a página privada. Tente novamente.";

            $this->redirecionar("pagina_privada/editar?id=" . ($this->dados['id'] ?? ''));
        } catch (\Exception $e) {
            error_log("Erro ao atualizar página privada: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao atualizar.";
            $this->redirecionar("pagina_privada/editar?id=" . ($this->dados['id'] ?? ''));
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['titulo'] ?? ''))) {
            $erros[] = "O campo 'Título' é obrigatório.";
        }

        if (empty(trim($dados['conteudo'] ?? ''))) {
            $erros[] = "O campo 'Conteúdo' é obrigatório.";
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function viewExiste(string $view): bool
    {
        $caminhoBase = __DIR__ . "/../../View/";
        $caminhoCompleto = $caminhoBase . str_replace(['.', '\\'], ['/', '/'], $view) . ".php";

        if (!file_exists($caminhoCompleto)) {
            error_log("View não encontrada: {$caminhoCompleto}");
            return false;
        }

        return true;
    }

    protected function redirecionar(string $rota): void
    {
        $urlDestino = defined("BASE_URL") ? BASE_URL . trim($rota, '/') : '/' . trim($rota, '/');

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("URL de redirecionamento inválida: {$urlDestino}");
            $_SESSION['msg_erro'] = "Erro ao redirecionar.";
            exit();
        }

        header("Location: {$urlDestino}", true, 302);
        exit();
    }

    private function validarCsrfToken(): void
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Requisição inválida (CSRF).";
            error_log("Tentativa de CSRF detectada.");
            $this->redirecionar("pagina_privada");
            exit();
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

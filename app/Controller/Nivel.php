<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\NivelModel;
use Core\ConfigView;

class Nivel extends Controller
{
    private NivelModel $nivel;
    private array $dados = [];

    public function __construct()
    {
        // ✅ Inicia a sessão apenas se não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();
        $this->nivel = new NivelModel();

        // ✅ Verifica se o usuário está logado
        if (empty($_SESSION['usuario_logado'])) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // ✅ Apenas administradores podem acessar
        if ($_SESSION['usuario_nivel'] !== 'admin') {
            $_SESSION['msg_erro'] = "Acesso negado. Você não tem permissão para visualizar esta página.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index(): void
    {
        $this->listar();

        try {
            $this->renderizarView("nivel/index", ['niveis' => $this->dados]);
        } catch (\Exception $e) {
            error_log("Erro ao carregar a view de níveis: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar a página.";
            $this->redirecionar("erro");
        }
    }

    private function listar(): void
    {
        try {
            $this->dados = $this->nivel->listar() ?? [];
            if (empty($this->dados)) {
                $_SESSION['msg_erro'] = "Nenhum nível encontrado.";
            }
        } catch (\Exception $e) {
            error_log("Erro ao listar níveis: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar os níveis.";
        }
    }

    public function atualizar(): void
    {
        $this->validarCsrfToken();

        $this->dados = filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'descricao' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];

        if (!$this->validarDados($this->dados)) {
            $this->redirecionar("nivel/editar?id=" . ($this->dados['id'] ?? ''));
            return;
        }

        try {
            $resultado = $this->nivel->atualizar($this->dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Nível atualizado com sucesso."
                : "Erro ao atualizar o nível. Tente novamente.";

            $this->redirecionar("nivel/editar?id=" . ($this->dados['id'] ?? ''));
        } catch (\Exception $e) {
            error_log("Erro ao atualizar nível: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao atualizar.";
            $this->redirecionar("nivel/editar?id=" . ($this->dados['id'] ?? ''));
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (!$dados['id'] || $dados['id'] <= 0) {
            $erros[] = "ID inválido.";
        }

        if (empty(trim($dados['nome']))) {
            $erros[] = "O campo 'Nome' é obrigatório.";
        } elseif (strlen($dados['nome']) > 255) {
            $erros[] = "O campo 'Nome' deve ter no máximo 255 caracteres.";
        }

        if (empty(trim($dados['descricao']))) {
            $erros[] = "O campo 'Descrição' é obrigatório.";
        } elseif (strlen($dados['descricao']) > 500) {
            $erros[] = "O campo 'Descrição' deve ter no máximo 500 caracteres.";
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }

    private function redirecionar(string $rota): void
    {
        // ✅ Certifica-se de que BASE_URL está definida antes de redirecionar
        $baseUrl = defined("BASE_URL") ? BASE_URL : "/";

        // ✅ Evita erro de headers já enviados
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Location: " . $baseUrl . trim($rota, '/'));
        exit();
    }

    private function validarCsrfToken(): void
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Requisição inválida.";
            error_log("Tentativa de CSRF detectada.");
            $this->gerarCsrfToken();
            $this->redirecionar("nivel");
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

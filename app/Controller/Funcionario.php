<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\FuncionarioModel;
use Core\ConfigView;

class Funcionario extends Controller
{
    private FuncionarioModel $funcionario;

    public function __construct()
    {
        // ✅ Inicia a sessão apenas se não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();
        $this->funcionario = new FuncionarioModel();

        // ✅ Verifica se o usuário está logado
        if (empty($_SESSION['usuario_logado'])) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // ✅ Apenas administradores podem acessar
        if ($_SESSION['usuario_nivel'] !== 'admin') {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem gerenciar funcionários.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index(): void
    {
        try {
            $dados['funcionarios'] = $this->funcionario->listar() ?? [];

            if (empty($dados['funcionarios'])) {
                $_SESSION['msg_erro'] = "Nenhum funcionário encontrado.";
            }

            $this->renderizarView("funcionario/index", $dados);
        } catch (\Exception $e) {
            error_log("Erro ao listar funcionários: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar funcionários.";
            $this->redirecionar("erro");
        }
    }

    public function atualizar(): void
    {
        $this->validarCsrfToken();

        $dados = filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'telefone' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'endereco' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'cargo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]) ?? [];

        if (!$this->validarDados($dados)) {
            $_SESSION['msg_erro'] = "Dados inválidos ou incompletos.";
            $this->redirecionar("funcionario/editar?id=" . ($dados['id'] ?? ''));
            return;
        }

        try {
            $resultado = $this->funcionario->atualizar($dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Funcionário atualizado com sucesso."
                : "Erro ao atualizar o funcionário. Tente novamente.";

            $this->redirecionar("funcionario/editar?id=" . $dados['id']);
        } catch (\Exception $e) {
            error_log("Erro ao atualizar funcionário: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao atualizar.";
            $this->redirecionar("funcionario/editar?id=" . $dados['id']);
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['nome'])) || strlen($dados['nome']) < 3) {
            $erros[] = "O campo 'Nome' é obrigatório e deve ter pelo menos 3 caracteres.";
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "O campo 'E-mail' é inválido.";
        }

        if (empty(trim($dados['telefone'])) || strlen($dados['telefone']) < 8) {
            $erros[] = "O campo 'Telefone' é obrigatório e deve ter pelo menos 8 caracteres.";
        }

        if (empty(trim($dados['endereco'])) || strlen($dados['endereco']) < 5) {
            $erros[] = "O campo 'Endereço' é obrigatório e deve ter pelo menos 5 caracteres.";
        }

        if (empty(trim($dados['cargo']))) {
            $erros[] = "O campo 'Cargo' é obrigatório.";
        }

        if ($erros) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function validarCsrfToken(): void
    {
        if (
            empty($_SESSION['csrf_token']) ||
            empty($_POST['csrf_token']) ||
            $_POST['csrf_token'] !== $_SESSION['csrf_token']
        ) {
            $_SESSION['msg_erro'] = "Requisição inválida. O token CSRF não corresponde.";
            error_log("Tentativa de CSRF detectada.");
            $this->gerarCsrfToken();
            $this->redirecionar("funcionario");
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }

    private function redirecionar(string $rota): void
    {
        $baseUrl = defined("BASE_URL") ? BASE_URL : "/";

        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Location: " . $baseUrl . trim($rota, '/'));
        exit();
    }
}

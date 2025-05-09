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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        parent::__construct();
        $this->nivel = new NivelModel();

        if (empty($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            error_log("Tentativa de acesso ao Nivel sem login.");
            $this->redirecionar("login");
            exit();
        }

        if (($_SESSION['usuario']['nivel_nome'] ?? '') !== 'admin') {
            $_SESSION['msg_erro'] = "Acesso negado. Você não tem permissão.";
            error_log("Acesso negado ao controlador Nivel.");
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
            error_log("Erro ao carregar index: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar página.";
            $this->redirecionar("erro");
        }
    }

    private function listar(): void
    {
        try {
            $this->dados = $this->nivel->listar() ?: [];
        } catch (\Exception $e) {
            error_log("Erro ao listar: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao listar níveis.";
        }
    }

    public function novo(): void
    {
        $this->gerarCsrfToken();
        $this->renderizarView("nivel/novo");
    }

    public function cadastrar(): void
    {
        $this->validarCsrfToken();
        $this->dados = filter_input_array(INPUT_POST, [
            'nome'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'status' => FILTER_DEFAULT
        ]) ?? [];

        // Valida nome
        if (empty(trim($this->dados['nome'] ?? '')) || strlen($this->dados['nome']) > 7) {
            $_SESSION['msg_erro'] = "Nome inválido. Máximo 7 caracteres.";
            $this->gerarCsrfToken();
            $this->redirecionar("nivel/novo");
            return;
        }

        $this->dados['status'] = in_array($this->dados['status'], ['ativo', 'inativo'], true)
            ? $this->dados['status']
            : 'ativo';

        if ($this->nivel->cadastrar($this->dados)) {
            $_SESSION['msg_sucesso'] = "Nível cadastrado com sucesso!";
            $this->redirecionar("nivel");
        } else {
            $_SESSION['msg_erro'] = "Erro ao cadastrar nível.";
            $this->gerarCsrfToken();
            $this->redirecionar("nivel/novo");
        }
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirecionar("nivel");
            return;
        }

        $nivel = $this->nivel->editar($id);
        if (!$nivel) {
            $_SESSION['msg_erro'] = "Nível não encontrado.";
            $this->redirecionar("nivel");
            return;
        }

        $this->gerarCsrfToken();
        $this->renderizarView("nivel/editar", ['nivel' => $nivel]);
    }

    public function atualizar(): void
{
    error_log("Entrou em Nivel::atualizar()");

    $this->validarCsrfToken();

    $dados = filter_input_array(INPUT_POST, [
        'id'     => FILTER_VALIDATE_INT,
        'nome'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'status' => FILTER_DEFAULT
    ]) ?? [];

    error_log("Dados recebidos: " . print_r($dados, true));

    if (!$this->validarDados($dados)) {
        error_log("Dados inválidos, redirecionando para editar novamente.");
        $this->gerarCsrfToken();
        $this->redirecionar("nivel/editar?id=" . ($dados['id'] ?? ''));
        return;
    }

    $dados['status'] = in_array($dados['status'], ['ativo','inativo'], true)
        ? $dados['status']
        : 'ativo';

    try {
        $stmt = $this->nivel->getConnection()->prepare(
            "UPDATE nivel SET nome = :nome, status = :status WHERE id = :id"
        );
        $executou = $stmt->execute([
            'id'     => $dados['id'],
            'nome'   => $dados['nome'],
            'status' => $dados['status'],
        ]);
        $afetadas = $stmt->rowCount();

        error_log("SQL executado. Afetadas: $afetadas");

    } catch (\Exception $e) {
        error_log("Erro SQL: " . $e->getMessage());
        $this->redirecionarComMensagem(
            "nivel/editar?id={$dados['id']}",
            "Erro interno ao atualizar nível. Tente novamente mais tarde.",
            "erro"
        );
        return;
    }

    if ($executou && $afetadas > 0) {
        $_SESSION['msg_sucesso'] = "Nível atualizado com sucesso.";
        error_log("Atualização concluída. Redirecionando para index.");
        $this->redirecionar("nivel"); // ISSO TEM QUE FUNCIONAR!!!!!
    } elseif ($executou && $afetadas === 0) {
        $_SESSION['msg_info'] = "Nenhuma alteração detectada.";
        error_log("Nenhuma linha alterada.");
        $this->redirecionar("nivel/editar?id=" . $dados['id']);
    } else {
        $_SESSION['msg_erro'] = "Erro ao atualizar nível.";
        error_log("Erro ao executar UPDATE.");
        $this->redirecionar("nivel/editar?id=" . $dados['id']);
    }
}


    public function excluir(): void
    {
        $this->validarCsrfToken();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirecionar("nivel");
            return;
        }
        if ($this->nivel->excluir($id)) {
            $_SESSION['msg_sucesso'] = "Nível excluído com sucesso.";
        } else {
            $_SESSION['msg_erro'] = "Falha ao excluir o nível.";
        }
        $this->redirecionar("nivel");
    }

    public function permissoes(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirecionar("nivel");
            return;
        }
        $nivel = $this->nivel->editar($id);
        $this->gerarCsrfToken();
        $this->renderizarView("nivel/permissoes", ['nivel' => $nivel]);
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];
        if (!$dados['id'] || $dados['id'] <= 0) {
            $erros[] = "ID inválido.";
        }
        if (empty(trim($dados['nome']))) {
            $erros[] = "O campo 'Nome' é obrigatório.";
        } elseif (strlen($dados['nome']) > 7) {
            $erros[] = "O campo 'Nome' deve ter no máximo 7 caracteres.";
        }
        if (!in_array($dados['status'], ['ativo', 'inativo'], true)) {
            $erros[] = "Status inválido.";
        }

        if ($erros) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }
        return true;
    }

    protected function renderizarView(string $view, array $dados = []): void
    {
        (new ConfigView($view, $dados))->renderizar();
    }

    protected function redirecionar(string $rota): void
    {
        $base = defined("BASE_URL") ? BASE_URL : "/";
        if (ob_get_length()) {
            ob_end_clean();
        }
        header("Location: {$base}{$rota}");
        exit();
    }

    private function validarCsrfToken(): void
    {
        $token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Requisição inválida.";
            $this->redirecionar("nivel");
        }
        unset($_SESSION['csrf_token']);
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

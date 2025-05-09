<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\FuncionarioModel;
use App\Model\CargoModel;
use App\Model\NivelModel;
use Core\ConfigView;

class Funcionario extends Controller
{
    private FuncionarioModel $funcModel;
    private CargoModel $cargoModel;
    private NivelModel $nivelModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();

        $this->funcModel  = new FuncionarioModel();
        $this->cargoModel = new CargoModel();
        $this->nivelModel = new NivelModel();

        if (empty($_SESSION['usuario']['logado']) || ($_SESSION['usuario']['nivel_nome'] !== 'admin')) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem gerenciar funcionários.";
            header("Location: " . BASE_URL);
            exit();
        }
    }

    public function index(): void
    {
        $dados['funcionarios'] = $this->funcModel->listar();
        $this->renderizarView("funcionario/index", $dados);
    }

    public function novo(): void
    {
        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "funcionario");
            exit();
        }

        $cargos = $this->cargoModel->listar();
        $niveis = $this->nivelModel->listar();

        $this->carregarModal('funcionario/novo_funcionario_modal', [
            'cargos' => $cargos,
            'niveis' => $niveis
        ]);
    }

    public function editar(): void
    {
        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "funcionario");
            exit();
        }

        $id   = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $func = $this->funcModel->obterPorId($id);

        if (!$func) {
            echo "<div class='alert alert-warning'>Funcionário não encontrado.</div>";
            return;
        }

        $cargos = $this->cargoModel->listar();
        $niveis = $this->nivelModel->listar();

        $this->carregarModal('funcionario/editar_funcionario_modal', [
            'funcionario' => $func,
            'cargos'      => $cargos,
            'niveis'      => $niveis
        ]);
    }

    public function salvar(): void
    {
        // ONLY AJAX POST
        if (!$this->isAjax() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        $dados = $_POST;

        if (empty($dados['nome']) || empty($dados['cargo_id']) || empty($dados['nivel_id'])) {
            $this->respostaJson(false, 'Campos obrigatórios não preenchidos.');
            return;
        }

        $dados['email']      = trim($dados['email'] ?? '');
        $dados['telefone']   = trim($dados['telefone'] ?? '');
        $dados['endereco']   = trim($dados['endereco'] ?? '');
        $dados['credencial'] = trim($dados['credencial'] ?? '');

        if (empty($dados['id'])) {
            // Cadastro novo
            if (empty($dados['credencial'])) {
                $this->respostaJson(false, 'Credencial é obrigatória para novo cadastro.');
                return;
            }

            if ($this->funcModel->credencialExiste($dados['credencial'])) {
                $this->respostaJson(false, 'Já existe um funcionário com esta credencial.');
                return;
            }

            // Tratamento de senha
            $senha = $dados['senha'] ?? '';
            $senhaRepetida = $dados['senha_repetida'] ?? '';

            if (empty($senha) || empty($senhaRepetida)) {
                $this->respostaJson(false, 'Senha e confirmação são obrigatórias.');
                return;
            }

            if ($senha !== $senhaRepetida) {
                $this->respostaJson(false, 'As senhas não coincidem.');
                return;
            }

            $dados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
            unset($dados['senha_repetida']);

            $dados['dt_registro'] = date('Y-m-d H:i:s');

            $ok  = $this->funcModel->cadastrar($dados);
            $msg = $ok ? 'Funcionário cadastrado com sucesso!' : 'Falha ao cadastrar funcionário.';
        } else {
            $ok  = $this->funcModel->atualizar($dados);
            $msg = $ok ? 'Funcionário atualizado com sucesso!' : 'Falha ao atualizar funcionário.';
        }

        $this->respostaJson($ok, $msg);
    }

    public function ver(): void
    {
        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "funcionario");
            exit();
        }

        $id   = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $func = $this->funcModel->obterPorId($id);

        if (!$func) {
            echo "<div class='alert alert-warning'>Funcionário não encontrado.</div>";
            return;
        }

        $this->carregarModal('funcionario/ver_funcionario_modal', [
            'funcionario' => $func
        ]);
    }

    public function excluir(): void
    {
        // GET AJAX carrega modal de confirmação
        if ($this->isAjax() && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $id   = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $func = $this->funcModel->obterPorId($id);
            if (!$func) {
                echo "<div class='alert alert-warning'>Funcionário não encontrado.</div>";
                return;
            }
            $this->carregarModal('funcionario/excluir_funcionario_modal', [
                'funcionario' => $func
            ]);
            return;
        }

        // POST AJAX executa exclusão
        if (!$this->isAjax() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $ok = $this->funcModel->excluir($id);

        $msg = $ok ? 'Funcionário excluído com sucesso!' : 'Falha ao excluir funcionário.';
        $this->respostaJson($ok, $msg);
    }

    protected function renderizarView(string $view, array $dados = []): void
    {
        (new ConfigView($view, $dados))->renderizar();
    }

    protected function carregarModal(string $view, array $dados): void
    {
        extract($dados, EXTR_SKIP);
        require __DIR__ . '/../View/' . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function respostaJson(bool $sucesso, string $mensagem, array $dados = []): void
    {
        parent::respostaJson($sucesso, $mensagem, $dados);
    }
}

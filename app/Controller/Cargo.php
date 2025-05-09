<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\CargoModel;
use Core\ConfigView;

class Cargo extends Controller
{
    private CargoModel $cargo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();
        $this->cargo = new CargoModel();

        if (empty($_SESSION['usuario_logado'])) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // Apenas adm
        if ($_SESSION['usuario_nivel'] !== 'admin') {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem gerenciar cargos.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index(): void
    {
        try {
            $dados['cargos'] = $this->cargo->listar() ?? [];

            if (empty($dados['cargos'])) {
                $_SESSION['msg_erro'] = "Nenhum cargo encontrado.";
            }

            $this->renderizarView("cargo/index", $dados);
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar a lista de cargos.");
        }
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido. Por favor, forneça um ID válido.";
            $this->redirecionar("cargo");
            return;
        }

        try {
            $cargo = $this->cargo->obterPorId($id);

            if (!$cargo) {
                $_SESSION['msg_erro'] = "Cargo não encontrado.";
                $this->redirecionar("cargo");
                return;
            }

            $this->renderizarView("cargo/editar", ['cargo' => $cargo]);
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar a página de edição do cargo.");
        }
    }

    public function atualizar(): void
    {
        $this->validarCsrfToken();

        $dados = $this->filtrarDadosCargo();

        if (!$dados || !$this->validarDados($dados)) {
            $_SESSION['msg_erro'] = "Dados inválidos. Verifique os campos e tente novamente.";
            $this->redirecionar("cargo/editar?id=" . ($dados['id'] ?? ''));
            return;
        }

        try {
            $cargoAtual = $this->cargo->obterPorId($dados['id']);

            if ($cargoAtual && json_encode($cargoAtual) === json_encode($dados)) {
                $_SESSION['msg_info'] = "Nenhuma alteração detectada.";
                $this->redirecionar("cargo/editar?id=" . $dados['id']);
                return;
            }

            $resultado = $this->cargo->atualizar($dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? "Cargo atualizado com sucesso."
                : "Erro ao atualizar o cargo. Tente novamente.";

            $this->redirecionar("cargo/editar?id=" . $dados['id']);
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao atualizar o cargo.");
        }
    }

    private function filtrarDadosCargo(): array
    {
        return filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'descricao' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]) ?? [];
    }

    private function validarDados(array $dados): bool
    {
        return !empty($dados['id']) && !empty($dados['nome']) && !empty($dados['descricao']) &&
            strlen($dados['nome']) <= 255 && strlen($dados['descricao']) <= 500;
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("Erro na CargoController: " . $e->getMessage());

        $_SESSION['msg_erro'] = $mensagemUsuario;
        $this->redirecionar("erro");
    }

    private function validarCsrfToken(): void
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Requisição inválida. O token CSRF não corresponde.";
            error_log("Tentativa de CSRF detectada.");
            $this->redirecionar("cargo");
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

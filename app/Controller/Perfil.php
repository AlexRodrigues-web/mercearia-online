<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\PerfilModel;
use Core\ConfigView;

class Perfil extends Controller
{
    private PerfilModel $perfil;
    private array $dados = [];

    public function __construct()
    {
        parent::__construct();

        if (empty($_SESSION['usuario_id'])) {
            $_SESSION['msg_error'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            return;
        }

        $this->perfil = new PerfilModel();
    }

    public function index()
    {
        try {
            $usuarioId = $_SESSION['usuario_id'] ?? null;

            if (!$usuarioId) {
                $this->redirecionar("home", "Perfil não encontrado.", "error");
                return;
            }

            $this->dados = $this->perfil->obterDadosUsuario($usuarioId);

            if (empty($this->dados) || !is_array($this->dados)) {
                $this->redirecionar("home", "Perfil não encontrado.", "error");
                return;
            }

            $view = new ConfigView("perfil/index", ["perfil" => $this->dados]);
            $view->renderizar();
        } catch (\Throwable $e) {
            error_log("Erro ao carregar perfil: " . $e->getMessage());
            $this->redirecionar("home", "Erro ao carregar perfil.", "error");
        }
    }

    public function editar()
    {
        try {
            $usuarioId = $_SESSION['usuario_id'] ?? null;

            if (!$usuarioId) {
                $this->redirecionar("login", "Você precisa estar logado para editar sua conta.", "error");
                return;
            }

            $this->dados = $this->perfil->obterDadosUsuario($usuarioId);

            if (empty($this->dados) || !is_array($this->dados)) {
                $this->redirecionar("perfil", "Dados do perfil não encontrados.", "error");
                return;
            }

            $view = new ConfigView("perfil/editar", ["perfil" => $this->dados]);
            $view->renderizar();
        } catch (\Throwable $e) {
            error_log("Erro ao carregar edição de perfil: " . $e->getMessage());
            $this->redirecionar("perfil", "Erro ao carregar edição.", "error");
        }
    }

    public function atualizar()
{
    try {
        $this->dados = $this->filtrarDadosPerfil();

        error_log("[DEBUG - PerfilController] Dados recebidos:");
        error_log(print_r($this->dados, true));

        if (!$this->dados || !$this->validarDados($this->dados)) {
            return $this->redirecionar("perfil/editar");
        }

        $token = $_POST['csrf_token'] ?? '';
        $resultado = $this->perfil->atualizar($this->dados, $token);

        error_log("[DEBUG - PerfilController] Resultado da atualização: " . ($resultado ? 'SUCESSO' : 'FALHA'));

        if ($resultado) {
            $_SESSION["msg_success"] = "Perfil atualizado com sucesso.";
            $_SESSION['usuario']['nome'] = $this->dados['nome'];

            if (!empty($_FILES['foto']['name'])) {
                $nomeFoto = $this->perfil->getNomeArquivoSalvo();
                if ($nomeFoto) {
                    $_SESSION['usuario']['foto'] = $nomeFoto;
                    error_log("[Perfil] ✅ Sessão atualizada com nova foto (armazenada no model): {$nomeFoto}");
                } else {
                    error_log("[Perfil] ⚠️ Nome do arquivo salvo não recuperado.");
                }
            }
            $this->redirecionar("perfil", "Perfil atualizado com sucesso.", "success");
        } else {
            $this->redirecionar("perfil/editar", "Erro ao atualizar perfil. Verifique os dados.", "error");
        }
    } catch (\Throwable $e) {
        error_log("[ERRO CRÍTICO - PerfilController] " . $e->getMessage());
        error_log($e->getTraceAsString());
        $_SESSION["msg_error"] = "Erro inesperado ao atualizar perfil.";
        $this->redirecionar("perfil/editar");
    }
}


    private function filtrarDadosPerfil(): array
    {
        $dados = filter_input_array(INPUT_POST, [
            "id" => FILTER_VALIDATE_INT,
            "nome" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "email" => FILTER_SANITIZE_EMAIL,
            "senhaAtual" => FILTER_UNSAFE_RAW,
            "senha" => FILTER_UNSAFE_RAW,
            "senhaRepetida" => FILTER_UNSAFE_RAW
        ]) ?? [];

        return [
            "id" => $dados["id"] ?? null,
            "nome" => trim($dados["nome"] ?? ""),
            "email" => $this->validarEmail($dados["email"] ?? "") ? strtolower(trim($dados["email"])) : "",
            "senhaAtual" => trim($dados["senhaAtual"] ?? ""),
            "senha" => trim($dados["senha"] ?? ""),
            "senhaRepetida" => trim($dados["senhaRepetida"] ?? "")
        ];
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty($dados["nome"])) {
            $erros[] = "O campo 'Nome' é obrigatório.";
        }

        if (empty($dados["email"]) || !$this->validarEmail($dados["email"])) {
            $erros[] = "O campo 'E-mail' é inválido.";
        }

        if (!empty($dados["senha"]) && $dados["senha"] !== $dados["senhaRepetida"]) {
            $erros[] = "As senhas não coincidem.";
        }

        if (!empty($erros)) {
            $_SESSION["msg_error"] = implode("<br>", $erros);
            return false;
        }

        return true;
    }

    private function validarEmail(string $email): bool
    {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function redirecionar(string $rota, string $mensagem = "", string $tipo = "info"): void
    {
        if (!empty($mensagem)) {
            $_SESSION["msg_{$tipo}"] = $mensagem;
        }

        header("Location: " . $this->getBaseUrl() . $rota);
        exit();
    }

    private function getBaseUrl(): string
    {
        $protocolo = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"] ?? "localhost";
        $scriptDir = trim(dirname($_SERVER["SCRIPT_NAME"]), "/");

        return "{$protocolo}://{$host}/" . ($scriptDir ? "{$scriptDir}/" : "");
    }
}

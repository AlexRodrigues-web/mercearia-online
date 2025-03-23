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

        // ✅ Verifica se o usuário está autenticado
        if (empty($_SESSION['usuario']['id'])) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
        }

        $this->perfil = new PerfilModel();
    }

    public function index()
    {
        try {
            $usuarioId = $_SESSION['usuario']['id'] ?? null;

            if (!$usuarioId) {
                $this->redirecionar("home", "Perfil não encontrado.", "erro");
            }

            $this->dados = $this->perfil->obterDadosUsuario($usuarioId);

            if (!$this->dados) {
                $this->redirecionar("home", "Perfil não encontrado.", "erro");
            }

            $view = new ConfigView("perfil/index", ["perfil" => $this->dados]);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao carregar perfil: " . $e->getMessage());
            $this->redirecionar("home", "Erro ao carregar perfil.", "erro");
        }
    }

    public function atualizar()
    {
        $this->dados = $this->filtrarDadosPerfil();

        if (!$this->dados || !$this->validarDados($this->dados)) {
            $this->redirecionar("perfil/editar?id=" . ($this->dados['id'] ?? 0), "Dados inválidos ou incompletos.", "erro");
        }

        try {
            $resultado = $this->perfil->atualizar($this->dados);

            $this->redirecionar(
                "perfil/editar?id=" . ($this->dados['id'] ?? 0),
                $resultado ? "Perfil atualizado com sucesso." : "Erro ao atualizar o perfil. Tente novamente.",
                $resultado ? "sucesso" : "erro"
            );
        } catch (\Exception $e) {
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            $this->redirecionar("perfil/editar?id=" . ($this->dados['id'] ?? 0), "Erro ao atualizar perfil.", "erro");
        }
    }

    private function filtrarDadosPerfil(): array
    {
        $dados = filter_input_array(INPUT_POST, [
            "id" => FILTER_VALIDATE_INT,
            "nome" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "email" => FILTER_SANITIZE_EMAIL,
            "telefone" => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            "endereco" => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];

        return [
            "id" => $dados["id"] ?? null,
            "nome" => trim($dados["nome"] ?? ""),
            "email" => $this->validarEmail($dados["email"] ?? "") ? strtolower(trim($dados["email"])) : "",
            "telefone" => trim($dados["telefone"] ?? ""),
            "endereco" => trim($dados["endereco"] ?? "")
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

        if (!empty($erros)) {
            $_SESSION["msg_erro"] = implode("<br>", $erros);
            return false;
        }

        return true;
    }

    private function validarEmail(string $email): bool
    {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function redirecionar(string $rota, string $mensagem = "", string $tipo = "info"): void
    {
        if (!empty($mensagem)) {
            $_SESSION["msg_{$tipo}"] = match ($tipo) {
                "erro" => method_exists($this, "alertaFalha") ? $this->alertaFalha($mensagem) : $mensagem,
                "sucesso" => method_exists($this, "alertaSucesso") ? $this->alertaSucesso($mensagem) : $mensagem,
                default => method_exists($this, "alertaPersonalizado") ? $this->alertaPersonalizado("info", $mensagem) : $mensagem,
            };
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

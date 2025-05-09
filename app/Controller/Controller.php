<?php  

namespace App\Controller;

use App\Model\AlertaModel;

class Controller
{
    private ?AlertaModel $alerta = null;
    protected string $baseUrl;

    public function __construct(?AlertaModel $alerta = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!defined("MERCEARIA2025")) {
            define("MERCEARIA2025", true);
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $this->baseUrl = rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/';

        $this->alerta = $alerta ?? new AlertaModel();

        $this->verificarPermissoes();
    }

    private function verificarPermissoes(): void
    {
        $rotaBruta = $_GET['url'] ?? 'home';
        $rotaTratada = strtolower(trim($rotaBruta, '/'));
        $rotaTratada = preg_replace('/\/index$/', '', $rotaTratada);

        error_log(">>> ROTA TRATADA NO CONTROLLER: " . $rotaTratada);

        $rotasPublicas = [
            "home", "produtos", "sobre", "contato", "login", "registro",
            "registro/cadastrar", "login/autenticar", "buscar", "erro", "paginaPublica", "ajuda",
            "institucional", "caixa/sucesso", "meuspedidos", "promocoes", "promoções", "promocao",
            "carrinho", "carrinho/index", "carrinho/adicionar", "carrinho/adicionarviaajax",
            "carrinho/remover", "carrinho/atualizar", "carrinho/aplicarCupom", "carrinho/finalizar",
            "carrinho/limpar", "carrinho/calcularFrete", "contato/enviar", "ajuda/enviar"
        ];

        if (strpos($rotaTratada, 'institucional/index') === 0) {
            return;
        }

        $rotasFuncionario = [
            "admin", "vendas", "usuario", "funcionario", "estoque", "fornecedor",
            "adminproduto", "adminvenda", "adminusuario",
            "relatorios", "configuracoes", "paginas"
        ];

        if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
            $_SESSION['usuario'] = [
                'logado' => false,
                'nivel_nome' => 'visitante',
                'paginas' => []
            ];
        }

        if ($_SESSION['usuario']['logado'] !== true && !in_array($rotaTratada, $rotasPublicas)) {
            error_log("Acesso negado: visitante tentando acessar '{$rotaTratada}'");

            if ($this->isAjax()) {
                http_response_code(401);
                $this->respostaJson(false, "Sessão expirada. Faça login novamente.");
            }

            $_SESSION['msg'] = "Você precisa fazer login para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        if (
            in_array($rotaTratada, $rotasFuncionario) &&
            !in_array($_SESSION['usuario']['nivel_nome'], ['funcionario', 'admin'])
        ) {
            $_SESSION['msg'] = "Acesso negado! Apenas funcionários têm acesso a esta área.";
            error_log("Acesso negado: '{$rotaTratada}' só pode ser acessado por funcionário/admin.");
            $this->redirecionar("erro/403");
            exit();
        }
    }

    protected function renderizarView(string $view, array $dados = []): void
    {
        extract($dados);
        $caminhoView = __DIR__ . "/../../app/View/" . $view . ".php";

        if (file_exists($caminhoView)) {
            require_once $caminhoView;
        } else {
            error_log("Erro: View não encontrada - " . $caminhoView);
            die("Erro ao carregar a página.");
        }
    }

    protected function alertaFalha(string $mensagem): string
    {
        return $this->getAlerta()->alertaFalha(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'));
    }

    protected function alertaSucesso(string $mensagem): string
    {
        return $this->getAlerta()->alertaSucesso(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'));
    }

    protected function alertaPersonalizado(string $tipo, string $mensagem): string
    {
        return $this->getAlerta()->gerarAlerta($tipo, htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'));
    }

    private function getAlerta(): AlertaModel
    {
        if ($this->alerta === null) {
            if (!class_exists(AlertaModel::class)) {
                error_log("Erro crítico: A classe 'AlertaModel' não foi encontrada.");
                throw new \Exception("Erro interno no sistema. Contate o suporte.");
            }
            error_log("Aviso: Dependência 'AlertaModel' não foi inicializada. Criando nova instância.");
            $this->alerta = new AlertaModel();
        }
        return $this->alerta;
    }

    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $tiposPermitidos = ['sucesso', 'erro', 'info', 'aviso'];
        if (!in_array($tipo, $tiposPermitidos, true)) {
            error_log("Aviso: Tipo de alerta desconhecido '{$tipo}'. Usando 'info'.");
            $tipo = 'info';
        }
        $_SESSION['msg'] = match ($tipo) {
            'sucesso' => $this->alertaSucesso($mensagem),
            'erro'    => $this->alertaFalha($mensagem),
            'aviso'   => $this->alertaPersonalizado('aviso', $mensagem),
            default   => $this->alertaPersonalizado($tipo, $mensagem),
        };
        $this->redirecionar($rota);
    }

    protected function redirecionar(string $rota): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }
        $rota = trim($rota, '/');
        $url  = $this->baseUrl . $rota;
        error_log("Redirecionando para: " . $url);
        if (!headers_sent()) {
            header("Location: " . $url);
            exit();
        }
        echo "<script>window.location.href='" . addslashes($url) . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url={$url}'></noscript>";
        exit();
    }

    protected function isAjax(): bool
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }

    protected function respostaJson(bool $sucesso, string $mensagem, array $dados = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $sucesso,
            'message' => $mensagem,
            'dados'   => $dados
        ]);
        exit;
    }

    protected function setMensagem(string $mensagem, string $tipo = 'info'): void
    {
        $map = ['danger' => 'erro', 'warning' => 'aviso'];
        $tipo = $map[$tipo] ?? $tipo;
        $tipos = ['sucesso','erro','info','aviso'];
        if (!in_array($tipo, $tipos, true)) {
            $tipo = 'info';
        }
        $_SESSION['msg'] = match ($tipo) {
            'sucesso' => $this->alertaSucesso($mensagem),
            'erro'    => $this->alertaFalha($mensagem),
            'aviso'   => $this->alertaPersonalizado('aviso', $mensagem),
            default   => $this->alertaPersonalizado($tipo, $mensagem),
        };
    }

    protected function redirect(string $rota): void
    {
        $this->redirecionar($rota);
    }

    protected function verificarTokenCSRF(): bool
    {
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSessao  = $_SESSION['csrf_token'] ?? '';
        return !empty($tokenEnviado) && !empty($tokenSessao) && hash_equals($tokenSessao, $tokenEnviado);
    }
}

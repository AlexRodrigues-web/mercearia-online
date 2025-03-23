<?php  

namespace App\Controller;

use App\Model\AlertaModel; // ✅ Correto!

class Controller
{
    private ?AlertaModel $alerta = null;
    protected string $baseUrl;

    public function __construct(?AlertaModel $alerta = null)
    {
        // ✅ Garante que a sessão está ativa antes de qualquer verificação
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ✅ Garante que a constante MERCEARIA2025 está definida
        if (!defined("MERCEARIA2025")) {
            define("MERCEARIA2025", true);
        }

        // ✅ Definir a Base URL corretamente, mantendo compatibilidade entre sistemas operacionais
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $this->baseUrl = rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/';

        // ✅ Instancia automaticamente a dependência se não for injetada
        $this->alerta = $alerta ?? new AlertaModel();

        // ✅ Verificação e Definição de Permissões
        $this->verificarPermissoes();
    }

    // ✅ Método para verificar permissões do usuário
    private function verificarPermissoes(): void
    {
        if (!isset($_SESSION['usuario_logado'])) {
            $_SESSION['usuario_logado'] = false;
            $_SESSION['usuario_nivel'] = 'visitante';
        }

        $rotasPublicas = ["home", "produtos", "sobre", "contato", "login"];
        $rotasFuncionario = ["admin", "produtos", "vendas", "usuarios"];
        $rotaAtual = strtolower($_GET['url'] ?? "home");

        if ($_SESSION['usuario_logado'] !== true && !in_array($rotaAtual, $rotasPublicas)) {
            $_SESSION['msg'] = "Você precisa fazer login para acessar esta página.";
            $this->redirecionar("login");
        }

        if ($_SESSION['usuario_nivel'] === 'comum' && in_array($rotaAtual, $rotasFuncionario)) {
            $_SESSION['msg'] = "Acesso negado! Apenas funcionários têm acesso a esta área.";
            $this->redirecionar("erro/403");
        }
    }

    // ✅ Método para renderizar as views corretamente
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

    // ✅ Métodos para geração de alertas
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

    // ✅ Retorna a instância de Alerta garantindo compatibilidade
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

    /**
     * ✅ Redireciona com uma mensagem de alerta
     */
    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $tiposPermitidos = ['sucesso', 'erro', 'info', 'aviso'];

        if (!in_array($tipo, $tiposPermitidos, true)) {
            error_log("Aviso: Tipo de alerta desconhecido '{$tipo}'. Usando 'info' como padrão.");
            $tipo = 'info';
        }

        $_SESSION['msg'] = match ($tipo) {
            'sucesso' => $this->alertaSucesso($mensagem),
            'erro' => $this->alertaFalha($mensagem),
            default => $this->alertaPersonalizado($tipo, $mensagem),
        };

        $this->redirecionar($rota);
    }

    /**
     * ✅ Redireciona para uma URL com suporte a fallback caso headers já tenham sido enviados.
     */
    protected function redirecionar(string $rota): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        $rota = trim($rota, '/');
        $url = $this->baseUrl . $rota;

        error_log("Redirecionando para: " . $url);

        if (!headers_sent()) {
            header("Location: " . $url);
            exit();
        } else {
            error_log("Aviso: Headers já foram enviados. Usando fallback para redirecionamento.");
            echo "<script>window.location.href='" . addslashes($url) . "';</script>";
            echo "<noscript><meta http-equiv='refresh' content='0;url={$url}'></noscript>";
            exit();
        }
    }
}

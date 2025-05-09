<?php  

namespace Core;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ConfigView
{
    private array $dados;
    private string $rota;

    public function __construct(string $rota, array $dados = [])
    {
        $this->rota = $rota;
        $this->dados = $dados;
    }

    public function renderizar(): void
    {
        if (!defined('BASE_URL')) {
            $this->definirBaseURL();
        }
        $caminhoArquivo = $this->obterCaminhoView();

        if (!$caminhoArquivo) {
            if ($this->rota === "home") {
                echo "<div class='alert alert-warning text-center'>Aviso: A página inicial ainda não foi configurada corretamente.</div>";
                return;
            }

            $this->registrarErro("Erro: View '{$this->rota}' não encontrada.");
            echo "<div class='alert alert-danger text-center'>Erro: A página solicitada não foi encontrada.</div>";
            return;
        }

        $dados = !empty($this->dados) && is_array($this->dados)
            ? $this->sanitizarDados($this->dados)
            : [];

        if (!empty($dados)) {
            extract($dados);
        }

        include_once $caminhoArquivo;
    }

    private function definirBaseURL(): void
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
        define('BASE_URL', "{$protocol}://{$host}/{$scriptDir}/");
    }

    /**
     * Obtém o caminho absoluto da View, validando o acesso seguro.
     *
     * @return string|null Retorna o caminho do arquivo se válido, ou `null` caso contrário.
     */
    private function obterCaminhoView(): ?string
    {
        $caminhoRaiz = realpath(__DIR__ . "/../app/View");
        $caminhoArquivo = realpath($caminhoRaiz . "/" . str_replace('.', '/', $this->rota) . ".php");

        // Garante que a View está dentro da pasta correta 
        if ($caminhoArquivo && strpos($caminhoArquivo, $caminhoRaiz) === 0) {
            return $caminhoArquivo;
        }

        return null;
    }

    private function sanitizarDados(array $dados): array
    {
        return array_map(function ($valor) {
            if (is_string($valor)) {
                return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($valor)) {
                return $this->sanitizarDados($valor); // Recursivo para arrays multidimensionais
            }
            return $valor;
        }, $dados);
    }

    private function registrarErro(string $mensagem): void
    {
        $logDir = __DIR__ . '/../logs';

        if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            error_log("Erro crítico: Falha ao criar diretório de logs.");
            return;
        }

        $logFile = $logDir . '/error.log';
        file_put_contents($logFile, date("[Y-m-d H:i:s] ") . $mensagem . PHP_EOL, FILE_APPEND);
    }
}

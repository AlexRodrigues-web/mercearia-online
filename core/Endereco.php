<?php 

namespace Core;

if (!defined("MERCEARIA2021")) // verificar se a constante criada no index, foi definida!
{   
    header("Location: http://localhost/mercearia/paginaInvalida/index");
    die("Erro: Página não encontrada!");
}

class ConfigView
{
    private $dados;
    private string $rota;

    public function __construct($rota, $dados = null)
    {
        $this->rota = $rota;
        $this->dados = $dados;        
    }

    public function renderizar()
    {
        // Sanitiza a rota para evitar inclusão de arquivos maliciosos
        $caminhoArquivo = 'app/' . $this->rota . '.php';

        if (file_exists($caminhoArquivo)) {
            if (is_array($this->dados)) {
                extract($this->dados, EXTR_OVERWRITE);
            }
            include_once $caminhoArquivo;
        } else {
            // Log de erro e redirecionamento amigável
            error_log("Erro ao carregar a view: " . $this->rota);
            header("Location: http://localhost/mercearia/paginaInvalida/index");
            exit;
        }
    }
}
?>

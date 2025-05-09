<?php 
namespace App\Model;

class AlertaModel
{
    /**
     * Retorna um elemento de alerta de falha.
     */
    public function alertaFalha(?string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaErro', $texto);
    }

    /**
     * Retorna um elemento de alerta de sucesso.
     */
    public function alertaSucesso(?string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaSucesso', $texto);
    }

    /**
     * Retorna um elemento de alerta de boas-vindas.
     */
    public function alertaBoasVindas(?string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaBoasVindas', $texto);
    }

    /**
     * Novo método genérico para compatibilidade com Controller
     * NÃO interfere nas rotas nem afeta views antigas.
     */
    public function gerarAlerta(string $tipo, string $mensagem): string
    {
        $mapaClasse = [
            'sucesso' => 'alertaSucesso',
            'erro'    => 'alertaErro',
            'info'    => 'alertaInfo',
            'aviso'   => 'alertaAviso'
        ];

        $classe = $mapaClasse[$tipo] ?? 'alertaInfo';
        return $this->criarElementoAlerta($classe, $mensagem);
    }

    /**
     * Criação do alerta HTML com segurança.
     */
    private function criarElementoAlerta(string $tipoClasse, ?string $texto): string
    {
        if (empty($texto)) {
            return '';
        }

        return '<div class="alert ' . htmlspecialchars($tipoClasse, ENT_QUOTES, 'UTF-8') . ' alert-dismissible fade show" role="alert">'
            . '<p class="text-center">' . htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>'
            . '</div>';
    }
}

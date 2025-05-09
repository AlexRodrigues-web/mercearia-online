<?php 
namespace App\Model;

class Alerta
{
    /**
     * Retorna um elemento de alerta de falha.
     *
     * @param string|null $texto Texto da mensagem.
     * @return string Elemento HTML do alerta de falha.
     */
    public function alertaFalha(?string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaErro', $texto);
    }

    /**
     * Retorna um elemento de alerta de sucesso.
     *
     * @param string|null $texto Texto da mensagem.
     * @return string Elemento HTML do alerta de sucesso.
     */
    public function alertaSucesso(?string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaSucesso', $texto);
    }

    /**
     * Retorna um elemento de alerta de boas-vindas.
     *
     * @param string|null $texto Texto da mensagem.
     * @return string Elemento HTML do alerta de boas-vindas.
     */
    public function alertaBoasVindas(?string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaBoasVindas', $texto);
    }

    /**
     * Método interno para criar elementos de alerta HTML.
     *
     * @param string $tipoClasse Classe CSS para o tipo de alerta.
     * @param string|null $texto Texto da mensagem.
     * @return string Elemento HTML do alerta ou string vazia se não houver texto.
     */
    private function criarElementoAlerta(string $tipoClasse, ?string $texto): string
    {
        if (empty($texto)) {
            return ''; // Retorna string vazia se não houver texto
        }

        return '<div class="alert ' . htmlspecialchars($tipoClasse, ENT_QUOTES, 'UTF-8') . ' alert-dismissible fade show" role="alert">'
            . '<p class="text-center">' . htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>'
            . '</div>';
    }
}

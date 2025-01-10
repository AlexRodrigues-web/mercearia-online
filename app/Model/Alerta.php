<?php 
namespace App\Model;

class Alerta
{
    /**
     * Retorna um elemento de alerta de falha
     *
     * @param string|null $texto Texto da mensagem
     * @return string Elemento HTML do alerta de falha
     */
    public function alertaFalha(string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaErro', $texto);
    }

    /**
     * Retorna um elemento de alerta de sucesso
     *
     * @param string|null $texto Texto da mensagem
     * @return string Elemento HTML do alerta de sucesso
     */
    public function alertaSucesso(string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaSucesso', $texto);
    }

    /**
     * Retorna um elemento de alerta de boas-vindas
     *
     * @param string|null $texto Texto da mensagem
     * @return string Elemento HTML do alerta de boas-vindas
     */
    public function alertaBemvindo(string $texto = null): string
    {
        return $this->criarElementoAlerta('alertaBemvindo', $texto);
    }

    /**
     * Método interno para criar elementos de alerta HTML
     *
     * @param string $tipoClasse Classe CSS para o tipo de alerta
     * @param string|null $texto Texto da mensagem
     * @return string Elemento HTML do alerta
     */
    private function criarElementoAlerta(string $tipoClasse, ?string $texto): string
    {
        return '<div class="alert ' . htmlspecialchars($tipoClasse) . ' alert-dismissible fade show" role="alert">'
            . '<p class="text-center">' . htmlspecialchars($texto) . '</p>'
            . '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            . '<span aria-hidden="true">&times;</span>'
            . '</button>'
            . '</div>';
    }
}

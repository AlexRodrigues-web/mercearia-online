<?php

namespace App\Model;

class Login extends Model
{
    private array $obrigatorio = ['credencial', 'senha', 'btnAcessar', 'csrf_token'];
    private int $quantidade_obrigatorio;
    private ?array $usuario = null;
    private array $credencial = [];
    private bool $formularioValido = false;
    private bool $resultado = false;
    private array $paginasUsuario = [];
    private array $paginasPublicas = [];

    /**
     * Realiza o login do usuário.
     *
     * @param array $dadosFormulario Dados enviados pelo formulário de login.
     * @return void
     */
    public function login(array $dadosFormulario): void
    {
        if (!method_exists($this, 'projetarEspecifico') || !method_exists($this, 'alertaFalha')) {
            throw new \Exception("Métodos essenciais da classe pai não foram encontrados.");
        }

        if (!$this->validarCSRF($dadosFormulario['csrf_token'] ?? '')) {
            $this->definirMensagemErro("Erro de segurança. Recarregue a página e tente novamente.");
            return;
        }

        $this->credencial['credencial'] = htmlspecialchars(trim($dadosFormulario['credencial'] ?? ''));
        $this->quantidade_obrigatorio = count($this->obrigatorio);

        // Validação dos campos obrigatórios
        $this->formularioValido = parent::existeCamposFormulario($dadosFormulario, $this->obrigatorio, $this->quantidade_obrigatorio);

        if ($this->formularioValido) {
            try {
                // Busca o usuário no banco de dados
                $this->usuario = parent::projetarEspecifico(
                    "SELECT f.id, f.nome, c.nome AS cargo, f.credencial, f.senha
                     FROM funcionario f
                     INNER JOIN cargo c ON f.cargo_id = c.id
                     WHERE f.credencial = :credencial LIMIT 1",
                    $this->credencial
                ) ?? [];

                if (!empty($this->usuario) && isset($this->usuario['senha'])) {
                    $this->validaUsuario(trim($dadosFormulario['senha'] ?? ''), $this->usuario['senha']);
                } else {
                    $this->definirMensagemErro("Usuário não encontrado.");
                }
            } catch (\PDOException $e) {
                $this->tratarErro("Erro no banco de dados ao tentar logar.", $e);
            }
        } else {
            $this->definirMensagemErro("Preencha todos os campos obrigatórios.");
        }
    }

    /**
     * Retorna o resultado da autenticação.
     *
     * @return bool
     */
    public function getResultado(): bool
    {
        return $this->resultado;
    }

    /**
     * Valida a senha do usuário e inicializa a sessão.
     *
     * @param string $senha Senha informada pelo usuário.
     * @param string|null $senhaBD Senha armazenada no banco de dados.
     * @return void
     */
    private function validaUsuario(string $senha, ?string $senhaBD): void
    {
        if (empty($senhaBD) || !password_verify($senha, $senhaBD)) {
            $this->definirMensagemErro("Senha inválida. Verifique e tente novamente.");
            return; // Evita `exit;` para não interromper a execução completa
        }

        $this->inicializarSessao();
        $this->resultado = true;
    }

    /**
     * Inicializa a sessão do usuário com os dados necessários.
     *
     * @return void
     */
    private function inicializarSessao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->gerarPaginas();
        $this->gerarNovoIdSessao();

        // Mantém outras variáveis da sessão, mas remove antigas de usuário
        unset($_SESSION['usuario_id'], $_SESSION['usuario_nome'], $_SESSION['usuario_cargo'], $_SESSION['usuario_paginas'], $_SESSION['paginas_publicas']);

        // Configura novos dados de sessão
        $_SESSION['usuario_id'] = $this->usuario['id'];
        $_SESSION['usuario_nome'] = $this->usuario['nome'];
        $_SESSION['usuario_cargo'] = $this->usuario['cargo'];
        $_SESSION['usuario_paginas'] = $this->paginasUsuario;
        $_SESSION['paginas_publicas'] = $this->paginasPublicas;

        $_SESSION['msg'] = method_exists($this, 'alertaBemvindo') 
            ? parent::alertaBemvindo("Bem-vindo, " . $_SESSION['usuario_nome'] . "!")
            : "Bem-vindo, " . $_SESSION['usuario_nome'] . "!";
    }

    /**
     * Gera as páginas de acesso do usuário.
     *
     * @return void
     */
    private function gerarPaginas(): void
    {
        if (!isset($this->usuario['id'])) {
            return;
        }

        $id['id'] = $this->usuario['id'];
        $paginas = new \App\Model\Paginas();
        $this->paginasUsuario = $paginas->acessoPaginas($id);
        $this->paginasPublicas = $paginas->listaPgPublicas();
    }

    /**
     * Gera um novo ID de sessão para evitar ataques de fixação de sessão.
     *
     * @return void
     */
    private function gerarNovoIdSessao(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Valida um token CSRF.
     *
     * @param string|null $tokenCSRF Token CSRF recebido do formulário.
     * @return bool
     */
    private function validarCSRF(?string $tokenCSRF): bool
    {
        return !empty($_SESSION['csrf_token']) && !empty($tokenCSRF) && hash_equals($_SESSION['csrf_token'], $tokenCSRF);
    }

    /**
     * Define uma mensagem de erro para a sessão.
     */
    private function definirMensagemErro(string $mensagem): void
    {
        $_SESSION['msg'] = "<div class='alert alert-danger'>$mensagem</div>";
    }

    /**
     * Trata erros no banco de dados e registra logs.
     *
     * @param string $mensagem Mensagem para o log de erro.
     * @param \PDOException $e Exceção capturada.
     * @return void
     */
    private function tratarErro(string $mensagem, \PDOException $e): void
    {
        error_log("[ERRO LOGIN] $mensagem - " . $e->getMessage());
        $this->definirMensagemErro("Erro interno no sistema. Por favor, tente novamente.");
    }
}

<?php
session_start(); // Garante que a sessão foi iniciada

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg_error'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Definir dinamicamente a BASE_URL correta
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 2)), '/'); // Corrigido para maior compatibilidade
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// Caminhos para os arquivos necessários
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';  // Caminho correto
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';      // Caminho correto
$customJsPath = $assetsPath . 'js/customPerfil.js';

// Gerar um token CSRF para proteção contra ataques, se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Definir variáveis para mensagens com fallback seguro
$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';

// Garantir que a variável $perfil esteja definida para evitar erros
$perfil = $perfil ?? [];

// Validar e sanitizar o ID do usuário antes de exibi-lo no formulário
$usuarioId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Scripts -->
    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Editar Perfil</h1>

        <!-- Mensagens de Feedback -->
        <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
            <div class="alert <?= !empty($msgSuccess) ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($msgSuccess ?: $msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg_success'], $_SESSION['msg_error']); ?>
        <?php endif; ?>

        <!-- Formulário de Edição -->
        <form action="<?= BASE_URL ?>/perfil/atualizar" method="POST" class="mt-4 shadow p-4 bg-light rounded">
            <!-- Proteção CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= $usuarioId ?>">

            <!-- Campo Credencial -->
            <div class="form-group">
                <label for="credencial" class="font-weight-bold">Credencial</label>
                <input 
                    type="text" 
                    name="credencial" 
                    id="credencial" 
                    class="form-control" 
                    value="<?= htmlspecialchars(trim($perfil['credencial'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="20" 
                    aria-label="Credencial do usuário">
                <small class="form-text text-muted">A credencial deve ter no máximo 20 caracteres.</small>
            </div>

            <!-- Campo Senha Atual -->
            <div class="form-group">
                <label for="senhaAtual" class="font-weight-bold">Senha Atual</label>
                <input 
                    type="password" 
                    name="senhaAtual" 
                    id="senhaAtual" 
                    class="form-control" 
                    placeholder="Digite sua senha atual" 
                    required 
                    aria-label="Senha atual do usuário">
            </div>

            <!-- Campo Nova Senha (Opcional) -->
            <div class="form-group">
                <label for="novaSenha" class="font-weight-bold">Nova Senha (Opcional)</label>
                <input 
                    type="password" 
                    name="senha" 
                    id="novaSenha" 
                    class="form-control" 
                    placeholder="Digite sua nova senha" 
                    maxlength="64" 
                    aria-label="Nova senha">
                <small class="form-text text-muted">Deixe em branco se não quiser alterar a senha.</small>
            </div>

            <!-- Campo Repetir Nova Senha -->
            <div class="form-group">
                <label for="repetirSenha" class="font-weight-bold">Repetir Nova Senha</label>
                <input 
                    type="password" 
                    name="senhaRepetida" 
                    id="repetirSenha" 
                    class="form-control" 
                    placeholder="Repita sua nova senha" 
                    maxlength="64" 
                    aria-label="Repetir nova senha">
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" aria-label="Atualizar perfil">Atualizar</button>
                <a href="<?= BASE_URL ?>/perfil" class="btn btn-secondary" aria-label="Cancelar edição do perfil">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Validação de Senhas no Front-end -->
    <script>
        document.querySelector("form").addEventListener("submit", function(event) {
            var senha = document.getElementById("novaSenha").value;
            var senhaRepetida = document.getElementById("repetirSenha").value;

            if (senha.length > 0 && senha !== senhaRepetida) {
                alert("A nova senha e a confirmação não coincidem.");
                event.preventDefault();
            }
        });
    </script>

</body>
</html>

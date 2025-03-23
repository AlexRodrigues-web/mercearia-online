<?php
session_start(); // Garante que a sessão foi iniciada

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Definir a URL base do projeto de forma dinâmica
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '<?= BASE_URL ?>';
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// Caminhos para os arquivos necessários
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';  // Caminho correto
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';      // Caminho correto
$customJsPath = $assetsPath . 'js/customPaginaPrivada.js';

// Verificar se os arquivos necessários existem
$arquivosNecessarios = [$cssBootstrapPath, $jsBootstrapPath, $customJsPath];
foreach ($arquivosNecessarios as $file) {
    if (!file_exists($file)) {
        error_log("Erro: Arquivo necessário ausente - $file");
    }
}

// Garantir que $pagina seja um array válido
if (!isset($pagina) || !is_array($pagina)) {
    error_log("Erro: Variável \$pagina não carregada corretamente.");
    $pagina = ['id' => '', 'nome' => ''];
}

// Gerar um token CSRF para proteção contra ataques
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Página Privada</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.min.js" defer></script>
    <script src="<?= BASE_URL ?>/app/Assets/js/customPaginaPrivada.js" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Editar Página Privada</h1>
        
        <!-- Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/pagina_privada/atualizar" method="POST" class="mt-4 shadow p-4 bg-light rounded">
            <input type="hidden" name="id" value="<?= htmlspecialchars($pagina['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Nome da Página -->
            <div class="form-group">
                <label for="nome" class="font-weight-bold">Nome da Página</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($pagina['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="30" 
                    placeholder="Digite o nome da página">
                <small class="form-text text-muted">O nome deve ter no máximo 30 caracteres.</small>
            </div>
            
            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="<?= BASE_URL ?>/pagina_privada" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

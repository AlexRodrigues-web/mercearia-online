<?php 
session_start(); // Garante que a sessão está iniciada corretamente

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Definir dinamicamente a URL base do projeto
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '<?= BASE_URL ?>';
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// Caminhos para os arquivos necessários
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'js/bootstrap.min.js';
$customJsPath = $assetsPath . 'js/customNivel.js';

// Verificação da existência dos arquivos, sem interromper a execução
$requiredFiles = [$cssBootstrapPath, $jsBootstrapPath, $customJsPath];
foreach ($requiredFiles as $file) {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($file, PHP_URL_PATH);
    if (!file_exists($filePath)) {
        error_log("Aviso: Arquivo necessário ausente - " . $file);
    }
}

// Garantir que os dados do nível estão disponíveis
if (!isset($nivel) || empty($nivel) || !isset($nivel['id'], $nivel['nome'])) {
    $_SESSION['msg'] = "Erro: Dados do nível não encontrados.";
    header("Location: /nivel");
    exit;
}

// Gerar token CSRF para segurança
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Nível</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">
    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Editar Nível</h1>

        <!-- Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg']) && is_string($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <form action="/nivel/atualizar" method="POST" class="mt-4 shadow p-4 bg-light rounded">
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            
            <!-- Campo ID (oculto) -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($nivel['id'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Nome do Nível -->
            <div class="form-group">
                <label for="nome" class="font-weight-bold">Nome do Nível</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($nivel['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="20" 
                    placeholder="Digite o nome do nível">
                <small class="form-text text-muted">O nome deve ter no máximo 20 caracteres.</small>
            </div>
            
            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="/nivel" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

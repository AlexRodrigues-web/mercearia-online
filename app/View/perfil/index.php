<?php
session_start(); // Sempre no topo do arquivo

// Inclui mensagens e realiza verificações
include_once '../include/mensagens.php';

// ============================
// 🔒 Verificação de Autenticação
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg_error'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// ============================
// 🌍 Definir `BASE_URL` Dinamicamente
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // Caminho ajustado
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ============================
// 📂 Caminhos dos Arquivos Necessários
// ============================
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';
$customJsPath = $assetsPath . 'js/customPerfil.js';

// ✅ Gerar Token CSRF para Segurança
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ✅ Definição Segura de Mensagens
$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

// ✅ Garantir que `$perfil` seja um array válido
$perfil = isset($perfil) && is_array($perfil) ? $perfil : null;
$perfilValido = $perfil && isset($perfil['nome'], $perfil['cargo'], $perfil['nivel']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>

    <!-- ✅ Inclusão Segura de CSS com Fallback -->
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- ✅ FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" defer></script>

    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Perfil do Usuário</h1>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
            <div class="alert <?= !empty($msgSuccess) ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($msgSuccess ?: $msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Cartão de Informações do Usuário -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-user-circle fa-5x text-secondary mb-3"></i>
                <h5 class="card-title text-secondary">Informações do Usuário</h5>

                <?php if ($perfilValido): ?>
                    <p class="card-text"><strong>Nome:</strong> <?= htmlspecialchars($perfil['nome'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Cargo:</strong> <?= htmlspecialchars($perfil['cargo'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Nível:</strong> <?= htmlspecialchars($perfil['nivel'], ENT_QUOTES, 'UTF-8') ?></p>
                    
                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>/perfil/editar" class="btn btn-primary" aria-label="Editar perfil">Editar Perfil</a>
                        <p class="text-muted mt-2">Caso necessário, atualize seus dados.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <strong>Erro:</strong> Não foi possível carregar as informações do perfil. Contate o suporte.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

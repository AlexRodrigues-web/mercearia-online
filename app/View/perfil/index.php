<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customPerfil.js';

// Garantir que os arquivos existem antes de incluí-los
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $cssBootstrapPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $jsBootstrapPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $customJsPath)) {
    die("Erro: Recursos necessários não encontrados. Contate o administrador do sistema.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Perfil do Usuário</h1>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Informações do Usuário</h5>
                <p><strong>Nome:</strong> <?= htmlspecialchars($perfil['nome'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Cargo:</strong> <?= htmlspecialchars($perfil['cargo'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Nível:</strong> <?= htmlspecialchars($perfil['nivel'], ENT_QUOTES, 'UTF-8') ?></p>
                <a href="/perfil/editar" class="btn btn-primary">Editar Perfil</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg_error'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 2)), '/');
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("[DEBUG - editar.php] Novo CSRF token gerado.");
}

$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
$mensagemFinal = is_array($msgSuccess ?: $msgError) ? implode(' ', $msgSuccess ?: $msgError) : ($msgSuccess ?: $msgError);

$perfil = $perfil ?? [];
$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-card {
            max-width: 600px;
            margin: auto;
        }
        .preview-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="form-card card shadow p-4">
        <h3 class="text-center text-primary mb-4">Atualização de Perfil</h3>

        <?php if (!empty($mensagemFinal)): ?>
            <div class="alert <?= $msgSuccess ? 'alert-success' : 'alert-danger' ?> text-center">
                <?= htmlspecialchars($mensagemFinal, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg_success'], $_SESSION['msg_error']); ?>
            <?php if ($msgSuccess): ?>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/perfil" class="btn btn-outline-primary">Voltar ao Perfil</a>
                </div>
                </div> <!-- Fecha o card para evitar mostrar o formulário novamente -->
            </div> <!-- Fecha container -->
            </body>
            </html>
            <?php return; endif; ?>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/perfil/atualizar" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="id" value="<?= $usuarioId ?>">

            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/app/Assets/image/perfil/<?= $perfil['foto'] ?? 'default.png' ?>" id="preview" class="preview-img mb-2">
                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                <small class="text-muted">JPG ou PNG, até 2MB.</small>
            </div>

            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($perfil['nome'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($perfil['email'] ?? '') ?>" required>
            </div>

            <hr>

            <div class="mb-3">
                <label for="senhaAtual" class="form-label">Senha Atual</label>
                <input type="password" class="form-control" id="senhaAtual" name="senhaAtual" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Nova Senha</label>
                <input type="password" class="form-control" id="senha" name="senha">
                <small class="text-muted">Deixe em branco para manter a senha atual.</small>
            </div>

            <div class="mb-3">
                <label for="senhaRepetida" class="form-label">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="senhaRepetida" name="senhaRepetida">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/perfil" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('foto').addEventListener('change', function (e) {
    const preview = document.getElementById('preview');
    const file = e.target.files[0];
    if (file) preview.src = URL.createObjectURL(file);
});

document.querySelector("form").addEventListener("submit", function (event) {
    const nova = document.getElementById("senha").value;
    const repete = document.getElementById("senhaRepetida").value;
    if (nova && nova !== repete) {
        alert("As senhas não coincidem.");
        event.preventDefault();
    }
});
</script>
</body>
</html>

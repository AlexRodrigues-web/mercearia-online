<?php
session_start();

// 🔒 Verifica se o usuário está autenticado e tem permissão de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] !== 'admin') {
    $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem editar usuários.";
    header("Location: /login");
    exit;
}

// 🌍 Define dinamicamente a BASE_URL
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// 📂 Caminhos dos arquivos de estilo e script
$assetsPath = BASE_URL . "/app/Assets/";

// ✅ Mensagens de feedback
$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

// ✅ Garantir que `$usuario` seja um array válido
$usuario = $usuario ?? null;

// 🚀 Gerar um token CSRF para proteção contra ataques
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário | Mercearia</title>

    <!-- ✅ Estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($assetsPath . 'bootstrap/js/bootstrap.bundle.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($assetsPath . 'js/customUsuario.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>

    <!-- ✅ Cabeçalho -->
    <?php include_once __DIR__ . "/../include/header.php"; ?>

    <div class="container mt-5">
        <h1 class="text-center text-primary"><i class="fas fa-user-edit"></i> Editar Usuário</h1>
        <p class="text-center text-muted">Atualize os dados do usuário no sistema.</p>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($msgSuccess)): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($msgSuccess, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($msgError)): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($usuario): ?>
        <!-- ✅ Formulário de Edição -->
        <form action="<?= BASE_URL ?>/usuario/atualizar" method="POST" class="mt-4 shadow p-4 bg-light rounded">
            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="nome" class="font-weight-bold">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required minlength="3" maxlength="70">
            </div>

            <div class="form-group">
                <label for="email" class="font-weight-bold">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="100">
            </div>

            <div class="form-group">
                <label for="nivel" class="font-weight-bold">Nível de Acesso</label>
                <select name="nivel" id="nivel" class="form-control" required>
                    <option value="admin" <?= ($usuario['nivel'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                    <option value="funcionario" <?= ($usuario['nivel'] === 'funcionario') ? 'selected' : '' ?>>Funcionário</option>
                    <option value="cliente" <?= ($usuario['nivel'] === 'cliente') ? 'selected' : '' ?>>Cliente</option>
                </select>
            </div>

            <div class="form-group">
                <label for="senha" class="font-weight-bold">Nova Senha <small class="text-muted">(opcional)</small></label>
                <input type="password" name="senha" id="senha" class="form-control" minlength="8" placeholder="Digite uma nova senha">
                <small class="form-text text-muted">Preencha apenas se deseja alterar a senha.</small>
            </div>

            <!-- ✅ Botões de Ação -->
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                <a href="<?= BASE_URL ?>/usuario" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>

        <?php else: ?>
            <div class="alert alert-warning text-center mt-3">
                <strong>Erro:</strong> Usuário não encontrado ou inválido.
            </div>
        <?php endif; ?>
    </div>

    <!-- ✅ Rodapé -->
    <?php include_once __DIR__ . "/../include/footer.php"; ?>

</body>
</html>

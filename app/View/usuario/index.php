<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 Verifica se o usuário está autenticado e tem permissão de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] !== 'admin') {
    $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem gerenciar usuários.";
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

// ✅ Garantir que `$usuarios` seja um array válido
$usuarios = $usuarios ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários | Mercearia</title>

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
        <h1 class="text-center text-primary"><i class="fas fa-users"></i> Lista de Usuários</h1>
        <p class="text-center text-muted">Gerencie os usuários cadastrados no sistema.</p>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
            <div class="alert <?= !empty($msgSuccess) ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($msgSuccess ?: $msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Tabela de Usuários -->
        <div class="table-responsive">
            <table class="table table-striped table-hover mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Nível</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(ucfirst($usuario['nivel']), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/usuario/editar?id=<?= urlencode($usuario['id']) ?>" class="btn btn-sm btn-warning" title="Editar Usuário">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/usuario/excluir?id=<?= urlencode($usuario['id']) ?>" class="btn btn-sm btn-danger btn-excluir" title="Excluir Usuário">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ✅ Botão para adicionar novo usuário -->
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/usuario/cadastrar" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Adicionar Usuário
            </a>
        </div>
    </div>

    <!-- ✅ Rodapé -->
    <?php include_once __DIR__ . "/../include/footer.php"; ?>

    <script>
        // 🔥 Confirmação antes de excluir um usuário
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".btn-excluir").forEach(function (btn) {
                btn.addEventListener("click", function (event) {
                    if (!confirm("Tem certeza que deseja excluir este usuário?")) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>

</body>
</html>

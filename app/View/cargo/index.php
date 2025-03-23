<?php
session_start(); // Sempre no topo do arquivo

include_once __DIR__ . '/../include/mensagens.php';

// ============================
// 🛡️ Segurança e Sessão
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = '<div class="alert alert-danger text-center">Por favor, faça login para acessar o sistema.</div>';
    header("Location: /login");
    exit();
}

// ============================
// 📂 Definição da BASE_URL Dinâmica
// ============================
if (!defined('BASE_URL')) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    define('BASE_URL', rtrim($protocolo . "://" . $host . dirname($scriptName), '/') . '/');
}

// ============================
// 🔑 Token CSRF Centralizado
// ============================
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// ============================
// 📌 Garante `$cargos` como array válido
// ============================
$cargos = isset($cargos) && is_array($cargos) ? $cargos : [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Cargos</title>
    
    <!-- Importação dinâmica dos estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'app/assets/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <script src="<?= htmlspecialchars(BASE_URL . 'app/assets/js/bootstrap.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars(BASE_URL . 'app/assets/js/customCargo.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4">Gestão de Cargos</h1>

        <!-- ✅ Exibição de Mensagens -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars(is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : $_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="mb-3">
            <a href="<?= htmlspecialchars(BASE_URL . 'cargo/cadastrar', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-success">Cadastrar Novo Cargo</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cargos)): ?>
                    <?php foreach ($cargos as $cargo): ?>
                        <?php if (isset($cargo['id'], $cargo['nome'])): ?>
                        <tr>
                            <td><?= htmlspecialchars($cargo['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($cargo['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center">
                                <!-- Botões de ação -->
                                <a href="<?= htmlspecialchars(BASE_URL . 'cargo/editar?id=' . $cargo['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm">Editar</a>

                                <!-- Formulário para Exclusão com Proteção CSRF -->
                                <form action="<?= htmlspecialchars(BASE_URL . 'cargo/excluir', ENT_QUOTES, 'UTF-8') ?>" method="POST" onsubmit="return confirmarExclusao();" class="d-inline">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($cargo['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Nenhum cargo cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmarExclusao() {
            return confirm('Deseja realmente excluir este cargo?');
        }
    </script>
</body>
</html>

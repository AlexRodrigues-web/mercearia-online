<?php
declare(strict_types=1);

// ============================
// 🛡️ Segurança e Sessão
// ============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================
// 📂 Definições de Caminho e URL Base
// ============================
define('APP_ROOT', dirname(__DIR__, 3)); // Define raiz do aplicativo
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$baseUrl = rtrim($protocolo . "://" . $host . dirname($scriptName), '/') . '/';
define('BASE_URL', $baseUrl);

// ============================
// ✅ Redirecionamento Seguro para Login
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = '<div class="alert alert-danger text-center">Por favor, faça o login para acessar o sistema.</div>';
    header("Location: " . BASE_URL . "login", true, 302);
    exit();
}

// ============================
// 🔑 Token CSRF Centralizado
// ============================
if (empty($_SESSION['token']) || !isset($_SESSION['csrf_time']) || time() - $_SESSION['csrf_time'] > 1800) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_time'] = time();
}

// ============================
// 🛡️ Garante Array Válido para Produtos
// ============================
$produtos = $produtos ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>app/assets/js/bootstrap.min.js" defer></script>
    <script src="<?= BASE_URL ?>app/assets/js/customCaixa.js" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4">Caixa</h1>

        <!-- ✅ Exibição de Mensagens -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center mt-3">
                <?= is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : $_SESSION['msg']; ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <!-- ✅ Alerta Caso Não Haja Produtos -->
        <?php if (empty($produtos)): ?>
            <div class="alert alert-warning text-center mt-3">
                Nenhum produto encontrado. Verifique a base de dados.
            </div>
        <?php else: ?>

        <!-- ✅ Formulário de Venda -->
        <form action="<?= BASE_URL ?>caixa/processarVenda" method="POST" class="mt-3">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="produto_id">Produto</label>
                <select name="produto_id" id="produto_id" class="form-control" required>
                    <option value="">Selecione um produto</option>
                    <?php foreach ($produtos as $produto): ?>
                        <option value="<?= htmlspecialchars($produto['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($produto['nome'] ?? 'Produto sem nome', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mt-3">
                <label for="quantidade">Quantidade</label>
                <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" required>
            </div>

            <div class="form-group mt-3">
                <label for="valor">Valor (€)</label>
                <input type="number" name="valor" id="valor" class="form-control" step="0.01" readonly>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Finalizar Venda</button>
            </div>
        </form>

        <?php endif; ?>
    </div>
</body>
</html>

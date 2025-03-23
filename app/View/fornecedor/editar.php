<?php
session_start();

// Verificação de autenticação do usuário
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Definir BASE_URL apenas se ainda não estiver definida
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '<?= BASE_URL ?>/app/Assets');
}

// Garantir que os dados do fornecedor estão definidos corretamente
if (!isset($fornecedor) || !is_array($fornecedor) || !isset($fornecedor['id'], $fornecedor['nome'], $fornecedor['nipc'])) {
    $_SESSION['msg'] = "Erro ao carregar os dados do fornecedor.";
    header("Location: /fornecedor");
    exit;
}

// Gerar token CSRF caso não exista
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Fornecedor</title>
    
    <!-- Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">

    <!-- Scripts -->
    <script src="<?= htmlspecialchars(BASE_URL . 'bootstrap/js/bootstrap.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars(BASE_URL . 'js/customFornecedor.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>

    <script>
        // Evita entrada de caracteres não numéricos no campo NIPC
        document.addEventListener("DOMContentLoaded", function () {
            const nipcInput = document.getElementById("nipc");
            if (nipcInput) {
                nipcInput.addEventListener("input", function () {
                    this.value = this.value.replace(/\D/g, '');
                });
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Editar Fornecedor</h1>

        <!-- Exibição de mensagem de erro ou sucesso -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <form action="/fornecedor/atualizar" method="POST">
            <!-- Token CSRF para proteção -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($fornecedor['id'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Nome -->
            <div class="form-group">
                <label for="nome">Nome do Fornecedor</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($fornecedor['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    maxlength="100"
                    required>
            </div>

            <!-- Campo NIPC -->
            <div class="form-group">
                <label for="nipc">NIPC</label>
                <input 
                    type="text" 
                    name="nipc" 
                    id="nipc" 
                    class="form-control" 
                    value="<?= htmlspecialchars($fornecedor['nipc'], ENT_QUOTES, 'UTF-8') ?>" 
                    pattern="\d{9}" 
                    title="Digite apenas números. Deve conter 9 dígitos."
                    required>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="/fornecedor" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

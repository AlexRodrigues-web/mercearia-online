<?php
session_start(); // Garante que a sessão foi iniciada

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg_error'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Definir dinamicamente a BASE_URL correta (evita caminhos quebrados)
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'], 2); // Caminho dinâmico ajustado
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// Caminhos para os arquivos necessários
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';  // Caminho correto
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';      // Caminho correto
$customJsPath = $assetsPath . 'js/customProduto.js';

// Definir mensagens para feedback
$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';

// Garantir que as variáveis $produto e $fornecedores estejam definidas e corretas
$produto = $produto ?? null;
$produtoValido = is_array($produto) && isset($produto['id'], $produto['nome'], $produto['preco'], $produto['quantidade'], $produto['fornecedor_id']);
$fornecedores = $fornecedores ?? [];

// Gerar um token CSRF para validação (caso não exista)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Scripts -->
    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Editar Produto</h1>

        <!-- Mensagens de Feedback -->
        <?php if (!empty($msgSuccess)): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($msgSuccess, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg_success']); ?>
        <?php endif; ?>

        <?php if (!empty($msgError)): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg_error']); ?>
        <?php endif; ?>

        <?php if ($produtoValido): ?>
        <!-- Formulário de Edição -->
        <form action="<?= BASE_URL ?>/produto/atualizar" method="POST" class="mt-4 shadow p-4 bg-light rounded">
            <input type="hidden" name="id" value="<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Nome -->
            <div class="form-group">
                <label for="nome" class="font-weight-bold">Nome do Produto</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="30"
                    placeholder="Digite o nome do produto"
                    aria-label="Nome do Produto">
            </div>

            <!-- Campo Preço -->
            <div class="form-group">
                <label for="preco" class="font-weight-bold">Preço (€)</label>
                <input 
                    type="number" 
                    name="preco" 
                    id="preco" 
                    class="form-control" 
                    value="<?= is_numeric($produto['preco']) ? htmlspecialchars(number_format($produto['preco'], 2, '.', ''), ENT_QUOTES, 'UTF-8') : '0.00' ?>" 
                    required 
                    step="0.01"
                    min="0.01"
                    placeholder="Digite o preço"
                    aria-label="Preço do Produto">
                <small class="form-text text-muted">Digite o preço no formato correto (ex: 10.50).</small>
            </div>

            <!-- Campo Fornecedor -->
            <div class="form-group">
                <label for="fornecedor_id" class="font-weight-bold">Fornecedor</label>
                <select name="fornecedor_id" id="fornecedor_id" class="form-control" required aria-label="Fornecedor">
                    <option value="">Selecione</option>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= htmlspecialchars($fornecedor['id'], ENT_QUOTES, 'UTF-8') ?>" 
                            <?= ($produto['fornecedor_id'] == $fornecedor['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fornecedor['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campo Quantidade -->
            <div class="form-group">
                <label for="quantidade" class="font-weight-bold">Quantidade</label>
                <input 
                    type="number" 
                    name="quantidade" 
                    id="quantidade" 
                    class="form-control" 
                    value="<?= is_numeric($produto['quantidade']) ? htmlspecialchars($produto['quantidade'], ENT_QUOTES, 'UTF-8') : '0' ?>" 
                    required 
                    min="0"
                    placeholder="Digite a quantidade"
                    aria-label="Quantidade do Produto">
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" aria-label="Atualizar Produto">Atualizar</button>
                <a href="<?= BASE_URL ?>/produto" class="btn btn-secondary" aria-label="Cancelar Edição">Cancelar</a>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <strong>Erro:</strong> Não foi possível carregar os dados do produto. Contate o suporte.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

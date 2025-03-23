<?php
session_start(); // Sempre no topo do arquivo

// Inclui mensagens e realiza verificações
include_once '../include/mensagens.php';

// ============================
// 🔒 Verificação de Autenticação
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// ============================
// 🌍 Definir `BASE_URL` Dinamicamente
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // Caminho dinâmico ajustado
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ============================
// 📂 Caminhos dos Arquivos Necessários
// ============================
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';
$customJsPath = $assetsPath . 'js/customNivel.js';

// ✅ Garantir que `$niveis` seja um array válido
$niveis = isset($niveis) && is_array($niveis) ? $niveis : [];

// ✅ Gerar Token CSRF para Segurança
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Níveis</title>

    <!-- ✅ CSS Bootstrap -->
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Gestão de Níveis</h1>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg']) && is_string($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>
        
        <!-- ✅ Botão para Cadastrar Novo Nível -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h3 class="text-secondary">Níveis Cadastrados</h3>
            <a href="<?= BASE_URL ?>/nivel/cadastrar" class="btn btn-success">Cadastrar Novo Nível</a>
        </div>

        <!-- ✅ Tabela de Níveis -->
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($niveis)): ?>
                    <?php foreach ($niveis as $nivel): ?>
                        <tr>
                            <td><?= htmlspecialchars($nivel['id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($nivel['nome'] ?? 'Sem Nome', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/nivel/editar?id=<?= urlencode($nivel['id'] ?? '') ?>" class="btn btn-primary btn-sm">Editar</a>
                                
                                <!-- ✅ Formulário de Exclusão com Método POST para Segurança -->
                                <form action="<?= BASE_URL ?>/nivel/excluir" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Deseja realmente excluir este nível?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($nivel['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Nenhum nível cadastrado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

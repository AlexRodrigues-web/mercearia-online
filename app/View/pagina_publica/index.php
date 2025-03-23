<?php
session_start(); // Garante que a sessão foi iniciada

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
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/'); // Corrigido para evitar erro de caminho
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// ============================
// 📂 Caminhos dos Arquivos Necessários
// ============================
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';
$customJsPath = $assetsPath . 'js/customPaginaPublica.js';

// ✅ Gerar Token CSRF para Segurança
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ✅ Definição Segura de Mensagens
$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

// ✅ Garantir que `$paginas` seja um array válido
$paginas = isset($paginas) && is_array($paginas) ? $paginas : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Páginas Públicas</title>

    <!-- ✅ Inclusão Segura de CSS com Fallback -->
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Gestão de Páginas Públicas</h1>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
            <div class="alert <?= !empty($msgSuccess) ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($msgSuccess ?: $msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Botão para Cadastrar Nova Página -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h3 class="text-secondary">Páginas Públicas Cadastradas</h3>
            <a href="<?= BASE_URL ?>/pagina_publica/cadastrar" class="btn btn-success" aria-label="Cadastrar nova página">
                Cadastrar Nova Página
            </a>
        </div>

        <!-- ✅ Tabela de Páginas -->
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($paginas)): ?>
                    <?php foreach ($paginas as $pagina): ?>
                        <tr>
                            <td><?= htmlspecialchars($pagina['id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($pagina['nome'] ?? 'Sem Nome', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/pagina_publica/editar?id=<?= urlencode($pagina['id'] ?? '') ?>" 
                                   class="btn btn-primary btn-sm" 
                                   aria-label="Editar página pública">Editar</a>

                                <!-- ✅ Formulário de Exclusão via POST com CSRF -->
                                <form action="<?= BASE_URL ?>/pagina_publica/excluir" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($pagina['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            aria-label="Excluir página pública"
                                            onclick="return confirm('Deseja realmente excluir esta página pública?');">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">Nenhuma página pública cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

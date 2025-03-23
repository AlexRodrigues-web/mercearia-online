<?php
session_start(); // Garante que a sessão foi iniciada

// ============================
// 🔒 Verificação de Autenticação
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'][] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// ============================
// 🌍 Definir `BASE_URL` Dinamicamente
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '<?= BASE_URL ?>';
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// ============================
// 📂 Caminhos dos Arquivos Necessários
// ============================
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';
$customJsPath = $assetsPath . 'js/customPaginaPrivada.js';

// ✅ Verificação da Existência dos Arquivos
$arquivosNecessarios = [$cssBootstrapPath, $jsBootstrapPath, $customJsPath];
$arquivosFaltantes = [];
foreach ($arquivosNecessarios as $file) {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . str_replace(BASE_URL, '', $file);
    if (!file_exists($filePath)) {
        error_log("Erro: Arquivo necessário ausente - $file");
        $arquivosFaltantes[] = basename($file);
    }
}

// ✅ Garantir que `$paginas` seja um array válido
$paginas = isset($paginas) && is_array($paginas) ? $paginas : [];

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
    <title>Gestão de Páginas Privadas</title>

    <!-- ✅ Inclusão Condicional de CSS -->
    <?php if (empty($arquivosFaltantes)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath, ENT_QUOTES, 'UTF-8') ?>">
    <?php else: ?>
        <style>
            body { background-color: #f8d7da; color: #721c24; font-family: Arial, sans-serif; }
            .container { margin-top: 50px; }
            .alert-warning { padding: 15px; border-radius: 5px; font-weight: bold; }
        </style>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 text-primary">Gestão de Páginas Privadas</h1>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <?php foreach ($_SESSION['msg'] as $msg): ?>
                <div class="alert alert-info text-center">
                    <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <!-- ✅ Exibição de Erro se Arquivos Estiverem Faltando -->
        <?php if (!empty($arquivosFaltantes)): ?>
            <div class="alert alert-danger text-center">
                <strong>Erro:</strong> Os seguintes arquivos estão ausentes e podem afetar a funcionalidade do site:
                <ul>
                    <?php foreach ($arquivosFaltantes as $arquivo): ?>
                        <li><?= htmlspecialchars($arquivo, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
                Contate o administrador do sistema.
            </div>
        <?php else: ?>
            <script src="<?= htmlspecialchars($jsBootstrapPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
            <script src="<?= htmlspecialchars($customJsPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
        <?php endif; ?>

        <!-- ✅ Botão de Cadastro -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h3 class="text-secondary">Páginas Privadas Cadastradas</h3>
            <a href="<?= BASE_URL ?>/pagina_privada/cadastrar" class="btn btn-success">Cadastrar Nova Página</a>
        </div>

        <!-- ✅ Tabela de Páginas -->
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table-dark">
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
                                <a href="<?= BASE_URL ?>/pagina_privada/editar?id=<?= urlencode($pagina['id'] ?? '') ?>" class="btn btn-primary btn-sm">Editar</a>

                                <!-- ✅ Formulário para Exclusão via POST com CSRF -->
                                <form action="<?= BASE_URL ?>/pagina_privada/excluir" method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir esta página privada?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($pagina['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Nenhuma página privada cadastrada.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

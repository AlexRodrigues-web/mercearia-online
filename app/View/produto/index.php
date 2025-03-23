<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================
// 🌍 Definir BASE_URL Corretamente
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

// ============================
// 📂 Caminhos dos Arquivos Necessários
// ============================
$assetsPath = BASE_URL . "app/Assets/";
$imagemPadrao = BASE_URL . "app/Assets/image/produtos/produto_default.jpg"; // Imagem padrão

// ✅ Definição Segura de Mensagens
$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

// ✅ Garantir que `$produtos` seja um array válido
$produtos = $produtos ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Mercearia</title>

    <!-- ✅ Estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- ✅ Barra de navegação -->
    <?php include_once __DIR__ . "/../include/header.php"; ?>

    <div class="container mt-5">
        <h1 class="text-center text-primary">Nossos Produtos</h1>
        <p class="text-center text-muted">Explore nossa seleção de produtos disponíveis.</p>

        <!-- ✅ Mensagens de Feedback -->
        <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
            <div class="alert <?= !empty($msgSuccess) ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($msgSuccess ?: $msgError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Produtos Listados -->
        <div class="row">
            <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow">
                            <?php 
                                // Diretório onde estão as imagens no servidor
                                $pastaImagens = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/";
                                $urlImagens = BASE_URL . "app/Assets/image/produtos/";

                                // Nome da imagem do produto
                                $imagemArquivo = !empty($produto['imagem']) ? trim($produto['imagem']) : '';
                                $imagemProduto = $imagemPadrao; // Começamos com a imagem padrão

                                // Verifica se a imagem existe no diretório
                                if (!empty($imagemArquivo) && file_exists($pastaImagens . $imagemArquivo)) {
                                    $imagemProduto = $urlImagens . rawurlencode($imagemArquivo);
                                }

                                // Debug: Verificar se a URL gerada está correta
                                error_log("Imagem carregada: " . $imagemProduto);
                            ?>

                            <img src="<?= htmlspecialchars($imagemProduto, ENT_QUOTES, 'UTF-8') ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                                <p class="card-text">R$ <?= is_numeric($produto['preco']) ? number_format((float)$produto['preco'], 2, ',', '.') : '0,00' ?></p>
                                <p class="card-text"><small class="text-muted">Fornecedor: <?= htmlspecialchars($produto['fornecedor'], ENT_QUOTES, 'UTF-8') ?></small></p>
                            </div>
                            <div class="card-footer text-center">
                                <?php if (!empty($_SESSION['usuario_logado'])): ?>
                                    <a href="<?= BASE_URL ?>produto/comprar?id=<?= urlencode($produto['id']) ?>" class="btn btn-success"><i class="fas fa-shopping-cart"></i> Comprar</a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>login" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Faça login para comprar</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">Nenhum produto disponível no momento.</p>
            <?php endif; ?>
        </div>

        <!-- ✅ Botão para Administradores (Adicionar Produtos) -->
        <?php if (!empty($_SESSION['usuario_logado']) && ($_SESSION['usuario_nivel'] === 'admin' || $_SESSION['usuario_nivel'] === 'funcionario')): ?>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>admin/produtos" class="btn btn-primary"><i class="fas fa-box"></i> Gerenciar Produtos</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- ✅ Rodapé -->
    <?php include_once __DIR__ . "/../include/footer.php"; ?>

    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($assetsPath . 'bootstrap/js/bootstrap.bundle.min.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>

<?php
// ✅ Garante que a segurança está ativa
if (!defined("MERCEARIA2025")) {
    die("Erro: Acesso não autorizado.");
}

// ✅ Verifica a BASE_URL
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ✅ Obtém os dados da página
$titulo = $dados['titulo'] ?? 'Ajuda';
$descricao = $dados['descricao'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?> | Mercearia</title>

    <!-- ✅ Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/css/mercearia.css">
</head>
<body>

    <!-- ✅ Inclusão do Header -->
    <?php include_once __DIR__ . '/../include/header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-primary text-center"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="text-center text-muted"><?= htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8') ?></p>

        <div class="row">
            <div class="col-md-6">
                <h3>Perguntas Frequentes</h3>
                <ul>
                    <li><strong>Como faço login no sistema?</strong><br> Basta acessar a página de login e inserir suas credenciais.</li>
                    <li><strong>Esqueci minha senha, o que fazer?</strong><br> Utilize a opção "Esqueci minha senha" na tela de login.</li>
                    <li><strong>Quem pode acessar o sistema?</strong><br> Administradores, funcionários e clientes cadastrados.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Entre em Contato</h3>
                <p>Se precisar de mais informações, entre em contato conosco:</p>
                <ul>
                    <li><i class="fas fa-envelope"></i> suporte@mercearia.com</li>
                    <li><i class="fas fa-phone"></i> (11) 99999-9999</li>
                    <li><i class="fas fa-map-marker-alt"></i> Rua Exemplo, 123 - São Paulo, SP</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ✅ Inclusão do Footer -->
    <?php include_once __DIR__ . '/../include/footer.php'; ?>

    <!-- ✅ Scripts -->
    <script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

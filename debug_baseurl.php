<?php
// Definição da BASE_URL (Simulação de como está sendo gerada no sistema)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define("BASE_URL", rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');

// Caminho do servidor para as imagens
$caminhoImagemFisico = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/produto_default.jpg";

// HTML para teste
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de BASE_URL</title>
</head>
<body>
    <h2>🛠️ Diagnóstico da BASE_URL</h2>

    <p><strong>BASE_URL Calculada:</strong> <?= BASE_URL ?></p>

    <h3>🖼️ Teste de imagem</h3>
    <p><strong>Imagem carregada diretamente:</strong></p>
    <img src="<?= BASE_URL ?>app/Assets/image/produtos/produto_default.jpg" width="200" alt="Teste Imagem">

    <h3>📂 Teste de Caminho do Servidor</h3>
    <p><strong>Caminho físico:</strong> <?= $caminhoImagemFisico ?></p>
    
    <h3>⚠️ Teste de Acesso ao Arquivo</h3>
    <?php if (file_exists($caminhoImagemFisico)): ?>
        <p style="color: green;"><strong>✅ A imagem existe no servidor.</strong></p>
    <?php else: ?>
        <p style="color: red;"><strong>❌ A imagem NÃO foi encontrada no servidor.</strong></p>
    <?php endif; ?>
</body>
</html>

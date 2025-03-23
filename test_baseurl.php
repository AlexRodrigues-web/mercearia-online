<?php
// Diagnóstico da BASE_URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$baseURL = "{$protocol}://{$host}{$scriptDir}/";

echo "<h2>🔍 Diagnóstico da BASE_URL</h2>";
echo "<p><b>BASE_URL Calculada:</b> <code>{$baseURL}</code></p>";

// Teste de imagem padrão
$imagemPadrao = $baseURL . "app/Assets/image/produtos/produto_default.jpg";

echo "<h3>🖼️ Teste de imagem</h3>";
echo "<p>Imagem carregada diretamente:</p>";
echo "<img src='{$imagemPadrao}' style='width: 300px; border: 2px solid red;'>";

// Teste de caminho físico
$caminhoImagem = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/produto_default.jpg";

echo "<h3>📂 Teste de Caminho do Servidor</h3>";
echo "<p><b>Caminho físico:</b> <code>{$caminhoImagem}</code></p>";

if (file_exists($caminhoImagem)) {
    echo "<p style='color: green;'>✅ A imagem existe no servidor.</p>";
} else {
    echo "<p style='color: red;'>❌ A imagem NÃO foi encontrada no servidor!</p>";
}

// Teste de acesso ao arquivo
$arquivoTeste = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/index.php";

echo "<h3>⚠️ Teste de Acesso ao Arquivo</h3>";
if (file_exists($arquivoTeste)) {
    echo "<p style='color: green;'>✅ O arquivo index.php existe.</p>";
} else {
    echo "<p style='color: red;'>❌ O arquivo index.php NÃO foi encontrado! O caminho do DOCUMENT_ROOT pode estar errado.</p>";
}

?>

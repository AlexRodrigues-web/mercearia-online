<?php
// ============================
// 🔒 Iniciar Sessão (SE NECESSÁRIO)
// ============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================
// 🌍 Garantir que BASE_URL e ASSETS_URL estejam definidas corretamente
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$basePath}", '/') . '/');
}

if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', BASE_URL . 'app/Assets');
}

// ============================
// 📂 Caminhos dos Arquivos de Assets
// ============================
$fontAwesomeJsPath = ASSETS_URL . '/fontawesome/js/all.min.js';
$customJsPath = ASSETS_URL . '/js/eventos.js';

// ============================
// 🌐 URL Base do Site para Compartilhamento
// ============================
$siteUrl = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia - Footer</title>

    <!-- ✅ Estilos e FontAwesome -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mercearia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* ✅ Correção do layout para manter o footer no final da tela */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        /* ✅ Mantém o conteúdo no centro e empurra o footer para baixo */
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
        }

        /* ✅ Garante que o footer fique sempre no final */
        footer {
            background: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            width: 100%;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="content">
            <!-- 🔹 O conteúdo principal da página vai aqui -->
        </div>

        <footer class="footer bg-light">
            <div class="container text-center">
                <!-- 🏆 Direitos Autorais -->
                <span class="text-muted d-block">&copy; <?= date('Y') ?> Mercearia. Todos os direitos reservados.</span>

                <!-- ✅ Links Úteis -->
                <div class="mt-2">
                    <a href="<?= BASE_URL ?>sobre" class="text-muted mx-2">Sobre</a>
                    <a href="<?= BASE_URL ?>contato" class="text-muted mx-2">Contato</a>
                </div>

                <!-- ✅ Redes Sociais -->
                <div class="mt-2">
                    <span class="text-muted">Compartilhe:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $siteUrl ?>" target="_blank" class="text-muted mx-2">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= $siteUrl ?>&text=Confira%20a%20Mercearia!" target="_blank" class="text-muted mx-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $siteUrl ?>&title=Mercearia" target="_blank" class="text-muted mx-2">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="https://wa.me/?text=Confira%20a%20Mercearia:%20<?= $siteUrl ?>" target="_blank" class="text-muted mx-2">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.instagram.com" target="_blank" class="text-muted mx-2">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>

                <!-- ✅ Contato -->
                <div class="mt-2">
                    <span class="text-muted">Contato: <a href="mailto:suporte@mercearia.com" class="text-muted">suporte@mercearia.com</a></span>
                </div>

                <!-- ✅ Endereço -->
                <div class="mt-2">
                    <span class="text-muted">Endereço: Rua da Bélgica, 2450, Canidelo, Vila Nova de Gaia</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- ✅ Scripts -->
    <script src="<?= $fontAwesomeJsPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>

</body>
</html>

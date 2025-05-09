<?php
session_start();

// ============================
// 🔄 Proteção contra Loop de Redirecionamento
// ============================
if (!defined("MERCEARIA_KEY")) {  
    if (!isset($_SESSION['redirected'])) {
        $_SESSION['redirected'] = true;
        header("Location: /erro?error=404"); 
        exit;
    } else {
        unset($_SESSION['redirected']); // Evita loop infinito
    }
}

// ============================
// 🔍 Tratamento de Erros
// ============================
$errorType = $_GET['error'] ?? '404';
$errorMessages = [
    '404' => ['title' => 'Erro 404 | Página Não Encontrada', 'message' => 'Desculpe! A página não existe ou foi removida.'],
    '500' => ['title' => 'Erro 500 | Erro Interno', 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'],
    '403' => ['title' => 'Erro 403 | Acesso Negado', 'message' => 'Você não tem permissão para acessar esta página.'],
    '400' => ['title' => 'Erro 400 | Requisição Inválida', 'message' => 'A solicitação feita é inválida. Verifique os parâmetros.'],
];

$errorInfo = $errorMessages[$errorType] ?? ['title' => 'Erro Desconhecido', 'message' => 'Ocorreu um erro inesperado.'];

// ============================
// 🌍 Definir BASE_URL Dinâmica
// ============================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = rtrim($protocol . '://' . $_SERVER['HTTP_HOST'] . '<?= BASE_URL ?>/app/Assets/', '/');

// ============================
// 📂 Lista de Arquivos Essenciais
// ============================
$requiredFiles = [
    'css/mercearia.css',
    'css/paginainvalida.css',
    'fontawesome/css/all.css',
    'image/logo/favicon.ico',
    'js/eventos.js',
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($errorInfo['title'], ENT_QUOTES, 'UTF-8') ?></title>

    <?php
    // ✅ Verifica se arquivos existem antes de incluir
    foreach ($requiredFiles as $file) {
        $localPath = $_SERVER['DOCUMENT_ROOT'] . "<?= BASE_URL ?>/app/Assets/" . $file;
        if (file_exists($localPath)) {
            $fileUrl = htmlspecialchars($baseUrl . '/' . $file, ENT_QUOTES, 'UTF-8');
            if (preg_match('/\.css$/', $file)) {
                echo "<link rel='stylesheet' href='$fileUrl'>" . PHP_EOL;
            } elseif (preg_match('/\.ico$/', $file)) {
                echo "<link rel='shortcut icon' href='$fileUrl'>" . PHP_EOL;
            } elseif (preg_match('/\.js$/', $file)) {
                echo "<script src='$fileUrl' defer></script>" . PHP_EOL;
            }
        } else {
            error_log("Recurso não encontrado: " . $file);
        }
    }
    ?>
</head>
<body>
    <!-- ✅ Cabeçalho -->
    <?php 
    $headerPath = $_SERVER['DOCUMENT_ROOT'] . "<?= BASE_URL ?>/app/View/include/header.php";
    if (file_exists($headerPath)) {
        include_once $headerPath;
    } else {
        echo '<div class="alert alert-warning text-center">⚠️ Cabeçalho não encontrado!</div>';
        error_log("Cabeçalho ausente: " . $headerPath);
    }
    ?>

    <div class="container-fluid">
        <div class="row">
            <!-- ✅ Menu Lateral -->
            <?php 
            $menuPath = $_SERVER['DOCUMENT_ROOT'] . "<?= BASE_URL ?>/app/View/include/menulateral.php";
            if (file_exists($menuPath)) {
                include_once $menuPath;
            } else {
                echo '<div class="alert alert-warning text-center">⚠️ Menu lateral não encontrado!</div>';
                error_log("Menu lateral ausente: " . $menuPath);
            }
            ?>

            <div class="col-md-9 ms-auto col-lg-10 principal">
                <main role="main">
                    <?php
                    // ✅ Exibe mensagens da sessão, se houver
                    if (!empty($_SESSION['msg'])) {
                        echo '<div class="alert alert-info text-center">';
                        echo is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8');
                        echo '</div>';
                        unset($_SESSION['msg']);
                    }
                    ?>  
                    <div class="alertaPagina text-center mt-5">
                        <h1><?= htmlspecialchars($errorInfo['message'], ENT_QUOTES, 'UTF-8') ?></h1>
                        <h5>Verifique se você digitou corretamente o endereço.</h5>
                        <a href="<?= isset($_SESSION['usuario_nome']) ? '/home/' : '/login/' ?>" class="btn btn-primary mt-3">
                            Ir para página inicial
                        </a>
                    </div>    
                </main>
            </div>
        </div>
    </div>

    <!-- ✅ Rodapé -->
    <?php 
    $footerPath = $_SERVER['DOCUMENT_ROOT'] . "<?= BASE_URL ?>/app/View/include/footer.php";
    if (file_exists($footerPath)) {
        include_once $footerPath;
    } else {
        echo '<div class="alert alert-warning text-center">⚠️ Rodapé não encontrado!</div>';
        error_log("Rodapé ausente: " . $footerPath);
    }
    ?>
</body>
</html>

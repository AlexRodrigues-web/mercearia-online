<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia sessão apenas se ainda não estiver ativa
}

// ✅ Se o usuário já está logado, redireciona para home/admin corretamente
if (!empty($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    $redirecionamento = ($_SESSION['usuario_nivel'] === 'funcionario' || $_SESSION['usuario_nivel'] === 'admin') 
        ? "admin" 
        : "home";
    
    header("Location: " . BASE_URL . $redirecionamento);
    exit();
}

// ============================
// 🌍 Definir `BASE_URL` Dinamicamente
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ============================
// 📂 Caminhos dos Assets
// ============================
$assetsPath = BASE_URL . "/app/Assets/";
$logoPath = $assetsPath . "image/logo/logo.jpg";

// 🛡️ Verificar se o Logo Existe
$logoFullPath = $_SERVER['DOCUMENT_ROOT'] . "/app/Assets/image/logo/logo.jpg";
if (!file_exists($logoFullPath)) {
    error_log("Aviso: O logo da empresa não foi encontrado. Usando logo padrão.");
    $logoPath = $assetsPath . "image/logo/default-logo.jpg";
}

// ============================
// 🔑 Gerar Token CSRF
// ============================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Mercearia</title>

    <!-- 📌 Bootstrap -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">

    <!-- 🎨 Estilos Personalizados -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- 📌 FontAwesome (para os ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- 🔗 Favicon -->
    <link rel="shortcut icon" href="<?= htmlspecialchars($assetsPath . 'image/logo/favicon.ico', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="text-center bg-light">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4 bg-white p-4 shadow rounded">
                <!-- O formulário envia para o controlador de autenticação -->
                <form class="form-signin" method="POST" action="<?= BASE_URL ?>/login/autenticar">
                    <img class="mb-4" id="logo" src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>" alt="Logo da Mercearia" style="height: 100px;">
                    <h1 class="h3 mb-3 font-weight-normal text-primary">Mercearia</h1>

                    <?php
                    // Exibição de mensagens de erro ou sucesso
                    if (!empty($_SESSION['msg_erro'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['msg_erro'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['msg_erro']);
                    }
                    if (!empty($_SESSION['msg_sucesso'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['msg_sucesso'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['msg_sucesso']);
                    }

                    // Recuperação de valores do formulário (em caso de erro)
                    $valorForm = $_SESSION['form_data'] ?? [];
                    unset($_SESSION['form_data']);
                    ?>

                    <!-- Campo para o token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-group">
                        <label for="inputCredencial">Credencial (Email ou Identificação)</label>
                        <input type="text" name="credencial" id="inputCredencial" class="form-control" 
                               value="<?= htmlspecialchars($valorForm['credencial'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="Digite sua credencial" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="inputPassword">Senha</label>
                        <input type="password" name="senha" id="inputPassword" class="form-control" 
                               placeholder="Digite sua senha" required>
                    </div>
                    
                    <button name="btnAcessar" type="submit" class="btn btn-primary btn-block btn-lg">Acessar</button>
                    
                    <div class="mt-3">
                        <p><a href="<?= BASE_URL ?>/login/esqueceuSenha" class="text-muted">Esqueceu a senha?</a></p>
                        <p class="text-muted">Ainda não tem conta? <a href="<?= BASE_URL ?>/registro" class="text-primary">Registre-se</a></p>
                    </div>
                </form>

                <!-- ✅ Bloco de Redes Sociais (Agora incluído na tela de login) -->
                <div class="mt-4">
                    <span class="text-muted">Siga-nos nas redes sociais:</span>
                    <div class="d-flex justify-content-center mt-2">
                        <a href="https://www.facebook.com" target="_blank" class="text-primary mx-2">
                            <i class="fab fa-facebook fa-2x"></i>
                        </a>
                        <a href="https://www.twitter.com" target="_blank" class="text-info mx-2">
                            <i class="fab fa-twitter fa-2x"></i>
                        </a>
                        <a href="https://www.instagram.com" target="_blank" class="text-danger mx-2">
                            <i class="fab fa-instagram fa-2x"></i>
                        </a>
                        <a href="https://www.linkedin.com" target="_blank" class="text-secondary mx-2">
                            <i class="fab fa-linkedin fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Scripts Corrigidos -->
    <script src="<?= htmlspecialchars($assetsPath . 'fontawesome/js/all.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Define BASE_URL
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ✅ Gera CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Caminho dos assets
$assetsPath = BASE_URL . "/app/Assets/";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Mercearia</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .form-group label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        input.form-control {
            margin-bottom: 15px;
        }

        .btn-block {
            width: 100%;
        }

        .text-primary {
            color: #009739 !important;
        }

        .bg-white {
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light text-center">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-5 bg-white p-4 shadow">
                <h2 class="text-primary mb-4"><i class="fas fa-user-plus me-2"></i>Criar Conta</h2>

                <!-- Mensagens -->
                <?php if (!empty($_SESSION['msg_erro'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['msg_erro'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['msg_erro']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['msg_sucesso'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['msg_sucesso'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['msg_sucesso']); ?>
                <?php endif; ?>

                <!-- Formulário -->
                <form action="<?= BASE_URL ?>/registro/cadastrar" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-group text-start">
                        <label for="nome"><i class="fas fa-user me-1"></i>Nome Completo</label>
                        <input type="text" name="nome" id="nome" class="form-control" required placeholder="Digite seu nome">
                    </div>

                    <div class="form-group text-start">
                        <label for="email"><i class="fas fa-envelope me-1"></i>E-mail</label>
                        <input type="email" name="email" id="email" class="form-control" required placeholder="Digite seu e-mail">
                    </div>

                    <div class="form-group text-start">
                        <label for="telefone"><i class="fas fa-phone me-1"></i>Telefone (opcional)</label>
                        <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="Ex: (351) 91234-5678">
                    </div>

                    <div class="form-group text-start">
                        <label for="senha"><i class="fas fa-lock me-1"></i>Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>

                    <div class="form-group text-start">
                        <label for="confirmar_senha"><i class="fas fa-lock me-1"></i>Confirmar Senha</label>
                        <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" required placeholder="Repita a senha">
                    </div>

                    <button type="submit" class="btn btn-success btn-block mt-3"><i class="fas fa-check-circle me-1"></i>Cadastrar</button>
                </form>

                <p class="mt-3">
                    Já tem uma conta? <a href="<?= BASE_URL ?>/login" class="text-primary">Faça login</a>.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Validação simples de senha
        document.querySelector("form").addEventListener("submit", function(e) {
            const senha = document.getElementById("senha").value;
            const confirmar = document.getElementById("confirmar_senha").value;

            if (senha !== confirmar) {
                e.preventDefault();
                alert("As senhas não coincidem.");
            }
        });
    </script>
</body>
</html>

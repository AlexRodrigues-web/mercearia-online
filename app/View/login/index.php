<?php 
// Verificar se a constante criada no index está definida
if (!defined("MERCEARIA2021")) {
    header("Location: /paginaInvalida/index");
    die("Erro: Página não encontrada!");
}

// Caminho correto para o logo
$logoPath = '/app/Assets/image/logo/logo.png';

// Garantir que o arquivo do logo existe antes de utilizá-lo
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $logoPath)) {
    die("Erro: O logo não foi encontrado. Contate o administrador do sistema.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Mercearia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="/app/Assets/css/mercearia.css">
    <link rel="stylesheet" href="/app/Assets/bootstrap/css/signin.css">
    <link rel="stylesheet" href="/app/Assets/fontawesome/css/all.css">
    <link rel="shortcut icon" href="/app/Assets/image/logo/favicon.ico">
</head>
<body class="text-center">
    <div class="container-fluid">
        <div class="row principal">
            <div class="col vh-100">
                <form class="form-signin" method="POST">
                    <img class="mb-4" id="logo" src="<?= $logoPath ?>" alt="Logo da Mercearia">
                    <h1 class="h3 mb-3 font-weight-normal">Mercearia</h1>

                    <?php
                    // Exibir mensagens de erro ou sucesso da sessão
                    if (isset($_SESSION['msg'])) {
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                    }

                    // Recuperar valores do formulário, se disponíveis
                    $valorForm = $this->dados['form'] ?? [];
                    ?>

                    <label for="inputCredencial" class="sr-only">Credencial</label>
                    <input type="text" name="credencial" id="inputCredencial" class="form-control" value="<?= htmlspecialchars($valorForm['credencial'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Credencial" required autofocus>
                    
                    <label for="inputPassword" class="sr-only">Senha</label>
                    <input type="password" name="senha" id="inputPassword" class="form-control" placeholder="Senha" required>
                    
                    <input name="btnAcessar" type="submit" class="btn btn-lg btn-primary btn-block" value="Acessar">
                    
                    <div class="mt-3 mb-3">
                        <p><a href="#">Esqueceu a senha?</a></p>
                    </div>
                </form>
                <?php include_once "app/View/include/footer.php"; ?>
            </div>
        </div>
    </div>
</body>
</html>

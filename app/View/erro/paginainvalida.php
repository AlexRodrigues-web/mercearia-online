<?php
// Verificar se a constante MERCEARIA2021 foi definida no index.php
if (!defined("MERCEARIA2021")) {
    header("Location: /paginaInvalida/index");
    die("Erro: Página não encontrada!");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 404 | Mercearia</title>

    <!-- Links CSS e Fontes -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="/app/Assets/css/mercearia.css">
    <link rel="stylesheet" href="/app/Assets/css/paginainvalida.css">
    <link rel="stylesheet" href="/app/Assets/fontawesome/css/all.css">
    <link rel="shortcut icon" href="/app/Assets/image/logo/favicon.ico">

    <!-- JavaScript -->
    <script src="/app/Assets/js/eventos.js" defer></script>
</head>
<body>
    <!-- Inclui o cabeçalho -->
    <?php include_once "app/View/include/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Inclui o menu lateral -->
            <?php include_once "app/View/include/menulateral.php"; ?>

            <div class="col-md-9 ml-auto col-lg-10 principal">
                <main role="main">
                    <?php
                    // Exibe mensagens da sessão, se houver
                    if (isset($_SESSION['msg'])) {
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                    }
                    ?>  
                    <div class="alertaPagina text-center mt-5">
                        <h1>Desculpe! A página não existe ou foi removida.</h1>
                        <h5>Verifique se você digitou corretamente o endereço.</h5>
                        <a href="<?= isset($_SESSION['usuario_nome']) ? '/home/' : '/login/' ?>" class="btn btn-primary mt-3">
                            Ir para página inicial
                        </a>
                    </div>    
                </main>
            </div>
        </div>
    </div>

    <!-- Inclui o rodapé -->
    <?php include_once "app/View/include/footer.php"; ?>
</body>
</html>

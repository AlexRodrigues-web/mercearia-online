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
<body>
    <header class="sticky-top">
        <nav class="navbar navbar-light bg-info">
            <div class="container-fluid">
                <a class="mx-auto" href="/">
                    <img src="<?= $logoPath ?>" alt="Logo da Mercearia" class="img-fluid">
                </a>
                <button class="navbar-toggler d-md-none collapsed" id="btn-menu" type="button" data-toggle="collapse" data-target="#menuLateral" aria-controls="menuLateral" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </header>

<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customPerfil.js';

// Garantir que os arquivos existem antes de incluí-los
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $cssBootstrapPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $jsBootstrapPath) || 
    !file_exists($_SERVER['DOCUMENT_ROOT'] . $customJsPath)) {
    die("Erro: Recursos necessários não encontrados. Contate o administrador do sistema.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Editar Perfil</h1>
        <form action="/perfil/atualizar" method="POST">
            <input type="hidden" name="id" value="<?= $_SESSION['usuario_id'] ?>">

            <div class="form-group">
                <label for="credencial">Credencial</label>
                <input 
                    type="text" 
                    name="credencial" 
                    id="credencial" 
                    class="form-control" 
                    value="<?= htmlspecialchars($perfil['credencial'], ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="20">
            </div>

            <div class="form-group">
                <label for="senhaAtual">Senha Atual</label>
                <input 
                    type="password" 
                    name="senhaAtual" 
                    id="senhaAtual" 
                    class="form-control" 
                    placeholder="Senha Atual" 
                    required>
            </div>

            <div class="form-group">
                <label for="novaSenha">Nova Senha</label>
                <input 
                    type="password" 
                    name="senha" 
                    id="novaSenha" 
                    class="form-control" 
                    placeholder="Nova Senha" 
                    required 
                    maxlength="64">
            </div>

            <div class="form-group">
                <label for="repetirSenha">Repetir Nova Senha</label>
                <input 
                    type="password" 
                    name="senhaRepetida" 
                    id="repetirSenha" 
                    class="form-control" 
                    placeholder="Repetir Nova Senha" 
                    required 
                    maxlength="64">
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/perfil" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>

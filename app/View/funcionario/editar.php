<?php
// Verificação de sessão e inclusão de arquivos essenciais
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login");
    exit;
}

// Caminhos corretos para os arquivos necessários
$cssBootstrapPath = '/app/Assets/css/bootstrap.min.css';
$jsBootstrapPath = '/app/Assets/js/bootstrap.min.js';
$customJsPath = '/app/Assets/js/customFuncionario.js';

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
    <title>Editar Funcionário</title>
    <link rel="stylesheet" href="<?= $cssBootstrapPath ?>">
    <script src="<?= $jsBootstrapPath ?>" defer></script>
    <script src="<?= $customJsPath ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Editar Funcionário</h1>
        <form action="/funcionario/atualizar" method="POST">
            <input type="hidden" name="id" value="<?= $funcionario['id'] ?>">

            <div class="form-group">
                <label for="nome">Nome</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($funcionario['nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    required>
            </div>
            <div class="form-group">
                <label for="cargo_id">Cargo</label>
                <select name="cargo_id" id="cargo_id" class="form-control" required>
                    <option value="">Selecione um cargo</option>
                    <?php foreach ($cargos as $cargo): ?>
                        <option 
                            value="<?= $cargo['id'] ?>" 
                            <?= $cargo['id'] == $funcionario['cargo_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cargo['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nivel_id">Nível de Acesso</label>
                <select name="nivel_id" id="nivel_id" class="form-control" required>
                    <option value="">Selecione um nível</option>
                    <?php foreach ($niveis as $nivel): ?>
                        <option 
                            value="<?= $nivel['id'] ?>" 
                            <?= $nivel['id'] == $funcionario['nivel_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nivel['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ativo">Ativo</label>
                <select name="ativo" id="ativo" class="form-control" required>
                    <option value="1" <?= $funcionario['ativo'] == 1 ? 'selected' : '' ?>>Sim</option>
                    <option value="0" <?= $funcionario['ativo'] == 0 ? 'selected' : '' ?>>Não</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/funcionario" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>

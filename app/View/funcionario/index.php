<?php
session_start();

// ============================
// 🔒 Verificação de Autenticação
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// ============================
// 🔑 Gerar Token CSRF Caso Não Exista
// ============================
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// ============================
// 🌍 Definir BASE_URL Dinâmica
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '<?= BASE_URL ?>/app/Assets/');
}

// ============================
// 🛡️ Garante que `$funcionarios` seja um array válido
// ============================
if (!isset($funcionarios) || !is_array($funcionarios)) {
    error_log("Erro: A variável \$funcionarios não foi definida corretamente no controller.");
    $funcionarios = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Funcionários</title>

    <!-- Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">

    <!-- Scripts -->
    <script src="<?= htmlspecialchars(BASE_URL . 'bootstrap/js/bootstrap.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars(BASE_URL . 'js/customFuncionario.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Gestão de Funcionários</h1>

        <!-- ✅ Exibição de Mensagens de Alerta -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars(is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : $_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <!-- ✅ Botão para Adicionar Novo Funcionário -->
        <div class="mb-3">
            <a href="/funcionario/cadastrar" class="btn btn-success">Adicionar Novo Funcionário</a>
        </div>

        <!-- ✅ Tabela de Funcionários -->
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Nível</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($funcionarios)): ?>
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <?php 
                        // ✅ Verificar se todas as chaves necessárias estão presentes
                        $camposNecessarios = ['id', 'nome', 'cargo', 'nivel', 'ativo'];
                        $erros = array_diff($camposNecessarios, array_keys($funcionario));
                        if (!empty($erros)) {
                            error_log("Erro: Faltam os seguintes índices no array \$funcionario: " . implode(', ', $erros));
                            continue;
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($funcionario['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($funcionario['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($funcionario['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($funcionario['nivel'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge <?= $funcionario['ativo'] == 1 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $funcionario['ativo'] == 1 ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/funcionario/editar?id=<?= htmlspecialchars($funcionario['id'], ENT_QUOTES, 'UTF-8') ?>" 
                                   class="btn btn-primary btn-sm">Editar</a>

                                <!-- ✅ Formulário para Exclusão com Proteção CSRF -->
                                <form action="/funcionario/excluir" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Deseja realmente excluir este funcionário?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Nenhum funcionário cadastrado.
                            <br>
                            <a href="/funcionario/cadastrar" class="btn btn-success btn-sm mt-3">
                                Adicionar Novo Funcionário
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
session_start(); // Sempre no topo do arquivo

include_once '../include/mensagens.php';

// ============================
// 🔒 Verificação de Autenticação
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// ============================
// 🌍 Definir BASE_URL Dinâmica
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // Caminho ajustado corretamente
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/app/Assets/");
}

// ============================
// 🛡️ Garante que `$fornecedores` seja um array válido
// ============================
$fornecedores = isset($fornecedores) && is_array($fornecedores) ? $fornecedores : [];

// ============================
// 🔑 Gerar Token CSRF Caso Não Exista
// ============================
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Fornecedores</title>
    
    <!-- Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">

    <!-- Scripts -->
    <script src="<?= htmlspecialchars(BASE_URL . 'bootstrap/js/bootstrap.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars(BASE_URL . 'js/customFornecedor.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Gestão de Fornecedores</h1>
        
        <!-- ✅ Exibição de Mensagens de Alerta -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars(is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : $_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <!-- ✅ Botão para Cadastrar Novo Fornecedor -->
        <div class="mb-3">
            <a href="/fornecedor/cadastrar" class="btn btn-success">Cadastrar Novo Fornecedor</a>
        </div>

        <!-- ✅ Tabela de Fornecedores -->
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>NIPC</th> <!-- Número de Identificação de Pessoa Coletiva -->
                    <th>Data de Registro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($fornecedores)): ?>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <?php 
                        // ✅ Verificar se todas as chaves necessárias estão presentes
                        if (!isset($fornecedor['id'], $fornecedor['nome'], $fornecedor['nipc'])) {
                            error_log("Erro: Registro de fornecedor inválido. Dados ausentes.");
                            continue; // Pula esse registro
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($fornecedor['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($fornecedor['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($fornecedor['nipc'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?= isset($fornecedor['dt_registro']) && !empty($fornecedor['dt_registro']) ? 
                                    htmlspecialchars(date('d/m/Y H:i', strtotime($fornecedor['dt_registro'])), ENT_QUOTES, 'UTF-8') : 
                                    'Data não disponível' ?>
                            </td>
                            <td>
                                <a href="/fornecedor/editar?id=<?= htmlspecialchars($fornecedor['id'], ENT_QUOTES, 'UTF-8') ?>" 
                                   class="btn btn-primary btn-sm">Editar</a>
                               
                                <!-- ✅ Formulário para Exclusão com Proteção CSRF -->
                                <form action="/fornecedor/excluir" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Deseja realmente excluir este fornecedor?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($fornecedor['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Nenhum fornecedor cadastrado. <br>
                            <a href="/fornecedor/cadastrar" class="btn btn-success btn-sm mt-3">
                                Adicionar Novo Fornecedor
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

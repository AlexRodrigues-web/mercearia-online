<?php
session_start();

// Obtém os dados do caixa, garantindo compatibilidade
$caixa = isset($dados) && is_array($dados) ? $dados : null;

// Caso os dados não estejam disponíveis, exibe mensagem e redireciona
if (!$caixa) {
    $_SESSION['msg'] = (new \App\Model\Alerta())->alertaFalha('Erro: Dados do caixa não encontrados.');
    echo $_SESSION['msg'];
    header("refresh:3;url=/caixa"); // Aguarda 3 segundos antes do redirecionamento
    exit;
}

// Garante que um token CSRF seja gerado apenas se ainda não existir
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Define a URL base dinamicamente
define('BASE_URL', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));

// Sanitiza os dados do caixa antes de exibir
$caixa_id = isset($caixa['id']) ? htmlspecialchars($caixa['id'], ENT_QUOTES, 'UTF-8') : '';
$caixa_nome = isset($caixa['nome']) ? htmlspecialchars($caixa['nome'], ENT_QUOTES, 'UTF-8') : '';
$caixa_descricao = isset($caixa['descricao']) ? htmlspecialchars($caixa['descricao'], ENT_QUOTES, 'UTF-8') : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Caixa</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/Assets/bootstrap/css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>app/Assets/bootstrap/js/bootstrap.min.js" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4">Editar Caixa</h1>

        <!-- Exibição de mensagens de alerta -->
        <?php
        if (isset($_SESSION['msg'])) {
            echo is_string($_SESSION['msg']) ? $_SESSION['msg'] : htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8');
            unset($_SESSION['msg']);
        }
        ?>

        <!-- Formulário para edição do caixa -->
        <form action="/caixa/atualizar" method="POST" class="mt-3">
            <!-- Proteção CSRF -->
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

            <!-- Campo oculto para o ID -->
            <input type="hidden" name="id" value="<?= $caixa_id ?>">

            <div class="form-group">
                <label for="nome">Nome do Caixa</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="nome" 
                    name="nome" 
                    value="<?= $caixa_nome ?>" 
                    placeholder="Digite o nome do caixa"
                    required>
            </div>

            <div class="form-group mt-3">
                <label for="descricao">Descrição</label>
                <textarea 
                    class="form-control" 
                    id="descricao" 
                    name="descricao" 
                    rows="3" 
                    placeholder="Digite uma descrição"
                    required><?= $caixa_descricao ?></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="/caixa" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

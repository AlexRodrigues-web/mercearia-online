<?php
session_start();

// Verifica se a sessão contém os dados necessários
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = (new \App\Model\Alerta())->alertaFalha('Por favor, faça login para acessar o sistema.');
    header("Location: /login");
    exit;
}

// Garante que `$dados` está definido e contém os dados do cargo
$cargo = isset($dados) && isset($dados['cargo']) ? $dados['cargo'] : null;

// Se os dados do cargo não foram carregados, exibe uma mensagem de erro e redireciona
if (!$cargo) {
    $_SESSION['msg'] = (new \App\Model\Alerta())->alertaFalha('Erro: Dados do cargo não encontrados.');
    header("Location: /cargo");
    exit;
}

// Garante que `BASE_URL` e `token CSRF` estão definidos corretamente
if (!defined('BASE_URL')) {
    define('BASE_URL', '<?= BASE_URL ?>'); 
}

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cargo</title>
    
    <!-- Importação dinâmica da URL base -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/Assets/bootstrap/css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>/Assets/bootstrap/js/bootstrap.min.js" defer></script>
    <script src="<?= BASE_URL ?>/Assets/js/customCargo.js" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4">Editar Cargo</h1>

        <!-- Exibição de mensagens de alerta -->
        <?= $_SESSION['msg'] ?? '' ?>
        <?php unset($_SESSION['msg']); ?>

        <!-- Formulário para edição do cargo -->
        <form action="/cargo/atualizar" method="POST" class="mt-3" onsubmit="return validarFormulario();">
            <!-- Proteção CSRF -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($cargo['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="nome">Nome do Cargo</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($cargo['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="Digite o nome do cargo"
                    required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="/cargo" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function validarFormulario() {
            let nome = document.getElementById("nome").value.trim();
            if (nome === "") {
                alert("O nome do cargo não pode estar vazio!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

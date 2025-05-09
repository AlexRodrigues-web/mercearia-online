<?php
// A sessão já está iniciada no bootstrap

// Se o usuário não estiver logado, redireciona para login
if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    header("Location: " . BASE_URL . "login");
    exit;
}

$nivel = $nivel ?? ['id' => 0, 'nome' => ''];

// Se vier ?ok=1, grava a mensagem de sucesso e redireciona para a listagem
if (isset($_GET['ok']) && $_GET['ok'] === '1') {
    $_SESSION['msg_sucesso'] = "✅ Código criptografado gerado com sucesso e enviado para o seu e-mail! Aguarde alguns instantes, em breve receberá um código que dará acesso ao painel!";
    header("Location: " . BASE_URL . "nivel");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Permissões do Nível</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>app/Assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function simularEnvio() {
            // Simula um delay e então redireciona incluindo o id do nível + ok=1
            setTimeout(() => {
                window.location.href = "<?= BASE_URL ?>nivel/permissoes?id=<?= $nivel['id'] ?>&ok=1";
            }, 800);
            return false; // cancela o envio real
        }
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>nivel">← Voltar a Níveis</a>
    </div>
</nav>

<main class="container py-5">
    <h2 class="text-center text-primary mb-4">
        Permissões do nível “<?= htmlspecialchars($nivel['nome'], ENT_QUOTES) ?>”
    </h2>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3 text-warning">🔐 Acesso restrito</h5>
                    <p class="card-text">
    🔒 <strong>Acesso Seguro:</strong> esta seção é restrita e requer um <strong>código de acesso</strong> para visualizar ou modificar permissões administrativas. <br><br>
    🔐 Sua senha será <strong>criptografada</strong> com tecnologia avançada e enviada de forma segura ao seu e-mail cadastrado. <br><br>
    🛡️ Garantimos total <strong>confidencialidade</strong> e <strong>proteção dos seus dados</strong>.
  </p>

                    <form onsubmit="return simularEnvio();">
                        <div class="mb-3">
                            <label for="email" class="form-label">Seu e-mail</label>
                            <input type="email" class="form-control" id="email" placeholder="voce@exemplo.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="senha" class="form-label">Confirme sua senha</label>
                            <input type="password" class="form-control" id="senha" placeholder="********" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            Gerar Código de Acesso
                        </button>
                    </form>

                    <div class="text-center text-muted mt-4 small">
                        🔒 Seus dados estão seguros e criptografados.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>

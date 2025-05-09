<?php
// app/View/adminusuario/editar.php
?>

<main class="container mt-4">
    <h1 class="mb-4">Editar Usuário</h1>

    <?php if (!empty($_SESSION['msg_erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            foreach ($_SESSION['msg_erro'] as $erro) {
                echo htmlspecialchars($erro) . '<br>';
            }
            unset($_SESSION['msg_erro']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['msg_sucesso'])): ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['msg_sucesso']);
            unset($_SESSION['msg_sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <form id="form-editar-usuario" action="<?= BASE_URL ?>adminusuario/salvar" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="nivel" class="form-label">Nível:</label>
            <select id="nivel" name="nivel" class="form-select" required>
                <option value="cliente" <?= $usuario['usuario_nivel'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                <option value="funcionario" <?= $usuario['usuario_nivel'] === 'funcionario' ? 'selected' : '' ?>>Funcionário</option>
                <option value="admin" <?= $usuario['usuario_nivel'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="<?= BASE_URL ?>adminusuario" class="btn btn-secondary">Cancelar</a>
    </form>
</main>

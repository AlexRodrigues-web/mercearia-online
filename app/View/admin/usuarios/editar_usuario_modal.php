<?php /** @var array|null $usuario */ ?>

<?php if (empty($usuario)): ?>
    <div class="alert alert-warning">
        Usuário não encontrado.
    </div>
<?php else: ?>
    <form id="formEditarUsuario" method="POST">
        <!-- ID do usuário -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8') ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input
                type="text"
                class="form-control"
                id="nome"
                name="nome"
                value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label for="nivel" class="form-label">Nível</label>
            <select
                class="form-select"
                id="nivel"
                name="nivel"
                required
            >
                <option value="">Selecione</option>
                <option value="cliente" <?= $usuario['usuario_nivel'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                <option value="funcionario" <?= $usuario['usuario_nivel'] === 'funcionario' ? 'selected' : '' ?>>Funcionário</option>
                <option value="admin" <?= $usuario['usuario_nivel'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <div class="text-end">
            <button
                type="button"
                class="btn btn-success"
                id="btnSalvarUsuario"
            >
                Salvar
            </button>
        </div>
    </form>
<?php endif; ?>

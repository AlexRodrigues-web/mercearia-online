<form id="formNovoUsuario" method="POST">
    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <div class="mb-3">
        <label for="senha" class="form-label">Senha</label>
        <input type="password" class="form-control" id="senha" name="senha" required>
    </div>

    <div class="mb-3">
        <label for="nivel" class="form-label">Nível</label>
        <select class="form-select" id="nivel" name="nivel" required>
            <option value="">Selecione</option>
            <option value="cliente">Cliente</option>
            <option value="funcionario">Funcionário</option>
            <option value="admin">Administrador</option>
        </select>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-success" id="btnSalvarNovoUsuario">
            Salvar
        </button>
    </div>
</form>

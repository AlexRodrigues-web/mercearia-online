<form id="form-salvar-funcionario" action="<?= BASE_URL ?>funcionario/salvar" method="POST">
  <div class="mb-3">
    <label for="nome" class="form-label fw-bold">Nome</label>
    <input type="text" id="nome" name="nome" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="credencial" class="form-label fw-bold">Credencial (login)</label>
    <input type="text" id="credencial" name="credencial" class="form-control" required minlength="4" maxlength="50">
  </div>

  <div class="mb-3">
    <label for="senha" class="form-label fw-bold">Senha</label>
    <input type="password" id="senha" name="senha" class="form-control" required minlength="6" maxlength="100">
  </div>

  <div class="mb-3">
    <label for="senha_repetida" class="form-label fw-bold">Confirmar Senha</label>
    <input type="password" id="senha_repetida" name="senha_repetida" class="form-control" required minlength="6" maxlength="100">
  </div>

  <div class="mb-3">
    <label for="cargo_id" class="form-label fw-bold">Cargo</label>
    <select id="cargo_id" name="cargo_id" class="form-select" required>
      <option value="">Selecione</option>
      <?php foreach ($cargos as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome'], ENT_QUOTES) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="nivel_id" class="form-label fw-bold">Nível</label>
    <select id="nivel_id" name="nivel_id" class="form-select" required>
      <option value="">Selecione</option>
      <?php foreach ($niveis as $n): ?>
        <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['nome'], ENT_QUOTES) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" value="1" id="ativo" name="ativo" checked>
    <label class="form-check-label" for="ativo">Ativo</label>
  </div>

  <div class="modal-footer">
  <button type="button" id="btnSalvar" class="btn btn-success">Cadastrar</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
  </div>
</form>

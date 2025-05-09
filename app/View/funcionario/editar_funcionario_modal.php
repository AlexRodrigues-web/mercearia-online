<form id="form-salvar-funcionario" action="<?= BASE_URL ?>funcionario/salvar" method="POST">
  <?php if (!empty($funcionario['id'])): ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id'], ENT_QUOTES) ?>">
  <?php endif; ?>

  <div class="mb-3">
    <label for="nome" class="form-label fw-bold">Nome</label>
    <input type="text" id="nome" name="nome" class="form-control"
           value="<?= isset($funcionario['nome']) ? htmlspecialchars($funcionario['nome'], ENT_QUOTES) : '' ?>" required>
  </div>

  <div class="mb-3">
    <label for="email" class="form-label fw-bold">E-mail</label>
    <input type="email" id="email" name="email" class="form-control"
           value="<?= isset($funcionario['email']) ? htmlspecialchars($funcionario['email'], ENT_QUOTES) : '' ?>">
  </div>

  <div class="mb-3">
    <label for="telefone" class="form-label fw-bold">Telefone</label>
    <input type="text" id="telefone" name="telefone" class="form-control"
           value="<?= isset($funcionario['telefone']) ? htmlspecialchars($funcionario['telefone'], ENT_QUOTES) : '' ?>">
  </div>

  <div class="mb-3">
    <label for="endereco" class="form-label fw-bold">Endereço</label>
    <input type="text" id="endereco" name="endereco" class="form-control"
           value="<?= isset($funcionario['endereco']) ? htmlspecialchars($funcionario['endereco'], ENT_QUOTES) : '' ?>">
  </div>

  <div class="mb-3">
    <label for="cargo_id" class="form-label fw-bold">Cargo</label>
    <select id="cargo_id" name="cargo_id" class="form-select" required>
      <option value="">Selecione</option>
      <?php foreach ($cargos as $c): ?>
        <option value="<?= $c['id'] ?>"
          <?= (isset($funcionario['cargo_id']) && $c['id'] == $funcionario['cargo_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="nivel_id" class="form-label fw-bold">Nível</label>
    <select id="nivel_id" name="nivel_id" class="form-select" required>
      <option value="">Selecione</option>
      <?php foreach ($niveis as $n): ?>
        <option value="<?= $n['id'] ?>"
          <?= (isset($funcionario['nivel_id']) && $n['id'] == $funcionario['nivel_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($n['nome'], ENT_QUOTES) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" value="1" id="ativo" name="ativo"
      <?= (isset($funcionario['ativo']) && $funcionario['ativo']) ? 'checked' : '' ?>>
    <label class="form-check-label" for="ativo">Ativo</label>
  </div>
</form>

<div class="modal-footer">
  <button type="button" id="btnSalvar" class="btn btn-primary">
    <?= isset($funcionario['id']) ? 'Atualizar' : 'Cadastrar' ?>
  </button>
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
</div>

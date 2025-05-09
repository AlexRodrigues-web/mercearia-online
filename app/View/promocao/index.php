<?php
if (!defined("MERCEARIA2025")) die("Acesso negado.");
$produtos = $dados['produtos'] ?? [];
$assetsPath = BASE_URL . "app/Assets/";
error_log("🧪 View REALMENTE USADA: promocao/index.php");
error_log("🛒 [View Promoção] Total de promoções recebidas: " . count($produtos));
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <title>🎯 Promoções Especiais | Mercearia Online</title>
  <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
  <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia-custom.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Poppins', sans-serif;
    }
    .logo {
      max-height: 70px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .hero-section {
      background: linear-gradient(90deg, #20c997, #198754);
      color: white;
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      margin-bottom: 2rem;
    }
    .carousel-inner img {
      height: 220px;
      object-fit: cover;
      border-radius: 12px;
    }
    .countdown {
      font-size: 1.4rem;
      margin-top: 1rem;
      font-weight: 600;
    }
    .card-produto {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, opacity 0.6s ease;
      opacity: 0;
      transform: translateY(20px);
    }
    .card-produto.show {
      opacity: 1;
      transform: translateY(0);
    }
    .card-produto:hover {
      transform: scale(1.03);
    }
    .badge-desconto {
      position: absolute;
      top: 10px;
      left: 10px;
      background: #dc3545;
      color: white;
      padding: 5px 10px;
      font-size: 0.85rem;
      border-radius: 8px;
      z-index: 1;
      cursor: pointer;
    }
    .extras {
      background: #e9f7ef;
      border-radius: 12px;
      padding: 2rem;
      margin-top: 3rem;
    }
    .dice-btn {
      margin-top: 1rem;
      font-size: 1.3rem;
    }
  </style>
</head>
<body>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container py-4">

  <?php if (!empty($_SESSION['msg'])): ?>
    <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
  <?php endif; ?>

  <div class="text-center mb-4">
    <img src="<?= $assetsPath ?>image/logo/logo.jpg" alt="Logo Mercearia" class="logo mb-3">
    <h2 class="fw-bold text-success">🌟 Semana de Super Ofertas</h2>
    <p class="text-muted mb-1">💥 Descontos que mudam tudo!</p>
    <p class="text-muted">Estoque e tempo limitados. Aproveite enquanto dura!</p>

    <button class="btn btn-warning dice-btn" onclick="sortearPromocaoAleatoria(this)">
      <i class="fas fa-dice me-2"></i>Tente a sorte!
    </button>
  </div>

  <div class="hero-section">
    <h1 class="display-6 fw-bold"><i class="fas fa-stopwatch me-2"></i>Descontos Ativos Agora</h1>
    <p>⚡ Atualizados em tempo real — só os mais rápidos aproveitam!</p>
    <div id="contadorPromocao" class="countdown">⏳ Verificando tempo restante...</div>
  </div>

  <div id="bannerCarrossel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php $banners = ['banner4.jpg','banner5.jpg','banner6.jpg'];
      foreach($banners as $i=>$banner): ?>
      <div class="carousel-item <?= $i===0?'active':'' ?>">
        <img src="<?= $assetsPath ?>image/banners/<?= $banner ?>" class="d-block w-100" alt="Banner <?= $i+1 ?>">
      </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarrossel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarrossel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  <?php if(empty($produtos)): ?>
    <div class="alert alert-warning text-center fs-5">Nenhuma promoção ativa. Volte em breve!</div>
  <?php else: ?>
    <div class="row justify-content-center g-4 text-center">
      <?php foreach($produtos as $idx=>$produto):
        $orig = $produto['preco'];
        $tipo = $produto['tipo'] ?? 'percentual';
        $d = $produto['desconto'] ?? 0;
        if($d<=0 && !in_array($tipo,['fretegratis','compre2leve3'])) continue;
        $promo = $tipo==='fixo' ? max(0,$orig-$d) : max(0,$orig*(1-($d/100)));
        $pct = $tipo==='fixo' ? round(100-($promo/$orig*100)) : round($d);
      ?>
      <div class="col-sm-8 col-md-6 col-lg-4">
        <div class="card card-produto position-relative h-100" data-index="<?= $idx ?>">
          <span class="badge-desconto" onclick="mostrarCupom(this,'<?= addslashes($produto['nome']) ?>')">
            -<?= $pct ?>%
          </span>
          <img src="<?= BASE_URL ?>app/Assets/image/produtos/<?= htmlspecialchars($produto['imagem']) ?>"
               class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>">
          <div class="card-body">
            <h6 class="fw-bold"><?= htmlspecialchars($produto['nome']) ?></h6>
            <p class="text-muted"><s>€ <?= number_format($orig,2,',','.') ?></s></p>
            <p class="text-success fs-5 fw-bold">€ <?= number_format($promo,2,',','.') ?></p>
            <button class="btn btn-outline-success btn-sm w-100" onclick='abrirModalCarrinho(<?= json_encode($produto) ?>)'>
              <i class="fas fa-cart-plus me-1"></i> Adicionar ao Carrinho
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="extras text-center">
    <h4 class="mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Quer mais oportunidades?</h4>
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="<?= BASE_URL ?>manutencao" class="btn btn-outline-dark">🔥 Mais vendidos</a>
      <a href="<?= BASE_URL ?>manutencao" class="btn btn-outline-dark">🆕 Novidades</a>
      <a href="<?= BASE_URL ?>manutencao" class="btn btn-outline-dark">💡 Dicas</a>
      <a href="<?= BASE_URL ?>manutencao" class="btn btn-outline-dark">📱 Cupom App</a>
    </div>
  </div>
</div>

<!-- Modal do Carrinho -->
<div class="modal fade" id="modalCarrinho" tabindex="-1" aria-labelledby="modalCarrinhoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formAdicionarCarrinho" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCarrinhoLabel">Adicionar ao Carrinho</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="produto_id" name="produto_id">
        <div class="mb-3">
          <label for="quantidade" class="form-label">Quantidade</label>
          <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" value="1" required>
        </div>
        <div class="mb-3">
          <label for="unidade" class="form-label">Unidade</label>
          <select name="unidade" id="unidade" class="form-select">
            <option value="unidade">Unidade</option>
            <option value="kg">Kg</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Adicionar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Sorteio de Cupom -->
<div class="modal fade" id="modalSorteio" tabindex="-1" aria-labelledby="modalSorteioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSorteioLabel">🎁 Parabéns!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p id="mensagemPromocao" class="fs-5 fw-semibold text-success"></p>
        <div class="input-group mt-3">
          <input type="text" id="cupomGerado" class="form-control text-center fw-bold" readonly>
          <button class="btn btn-outline-secondary" onclick="copiarCupom()">Copiar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $assetsPath ?>js/eventos.js?v=<?= time() ?>"></script>
<script>
const fim = new Date("<?= date('Y-m-d\TH:i:s', strtotime($produtos[0]['fim'] ?? '2025-12-31T23:59:00')) ?>").getTime();
const cnt = document.getElementById('contadorPromocao');
setInterval(() => {
  const now = Date.now(), diff = fim - now;
  if (diff < 0) { cnt.innerText = '⛔ Promoção encerrada'; return; }
  const d = Math.floor(diff / 86400000),
        h = Math.floor((diff % 86400000) / 3600000),
        m = Math.floor((diff % 3600000) / 60000),
        s = Math.floor((diff % 60000) / 1000);
  cnt.innerText = `${d}d ${h}h ${m}m ${s}s restantes`;
}, 1000);

window.addEventListener('load', () => {
  document.querySelectorAll('.card-produto').forEach((c, i) => {
    setTimeout(() => c.classList.add('show'), 150 * i);
  });
});

function mostrarCupom(el, nome) {
  el.classList.add('pulse');
  el.style.background = '#ffc107'; el.style.color = '#000';
  setTimeout(() => {
    el.style.background = '#dc3545';
    el.style.color = '#fff';
    el.classList.remove('pulse');
  }, 3000);
  alert(`💡 Oferta em ${nome}: CUPOM20 no checkout!`);
}

function sortearPromocaoAleatoria(btn) {
  const promocoes = [
    { titulo: "🎯 Você ganhou 10% de desconto!", cupom: "PROMO10" },
    { titulo: "🎯 Compre 3 e pague 2!", cupom: "COMPRE3" },
    { titulo: "🎯 Frete grátis garantido!", cupom: "FRETEGRATIS" },
    { titulo: "🎯 Cupom de 20% OFF ativado!", cupom: "PROMO20" },
    { titulo: "🎯 Você ganhou €30 de desconto!", cupom: "VALE30" },
    { titulo: "🎯 Até 50% de desconto!", cupom: "SURPRESA" }
  ];
  const sel = promocoes[Math.floor(Math.random() * promocoes.length)];
  const codigo = sel.cupom + Math.floor(100 + Math.random() * 900);
  document.getElementById('mensagemPromocao').innerText = sel.titulo;
  document.getElementById('cupomGerado').value = codigo;
  const modal = new bootstrap.Modal(document.getElementById('modalSorteio'));
  modal.show();
}

function copiarCupom() {
  const input = document.getElementById('cupomGerado');
  input.select();
  input.setSelectionRange(0, 99999);
  document.execCommand("copy");
  alert("✅ Cupom copiado: " + input.value);
}
</script>

</body>
</html>

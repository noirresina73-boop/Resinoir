<?php

use Controllers\ListController;

include 'autoloader.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Resinoir — Wireframe Mobile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<svg width="0" height="0" style="position:absolute">
  <defs>
    <clipPath id="gothicArch" clipPathUnits="objectBoundingBox">
      <path d="M0,0.30 C0,0.09 0.19,0 0.5,0 C0.81,0 1,0.09 1,0.30 L1,1 L0,1 Z"/>
    </clipPath>
  </defs>
</svg>

<div class="device">

  <!-- TOP NAV -->
  <navbar class="topnav">
    <div class="icon-btn">
    </div>
    <div class="wordmark">Resinoir</div>
    <div class="icon-btn">
    </div>
  </navbar>

  <!-- HEADER -->
  <div class="header">
    <div class="eyebrow">Catálogo</div>
    <h1>Catálogo<br>completo</h1>
    <div class="sub">Todas nossas peças, moldadas à mão e inspiradas na arquitetura gótica</div>
  </div>





  <!-- CATALOGO GERAL -->
  <div class="catalog-section">
    <div class="eyebrow">Produtos</div>
    <div class="product-grid">

      <?php
        $Listar = new ListController;
        $Listar = $Listar->listProdutos('catalogo');
      ?>


      <!-- <div class="product-card">
        <div class="product-photo">
          <div class="product-badge">Novo</div>
          <div class="wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg></div>
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1"><path d="M12 3v18M6 8l6-5 6 5"/></svg>
        </div>
        <div class="product-info">
          <div class="name">Colar Cruz Rubi</div>
          <div class="price">R$ 142,00</div>
          <div class="rating"><svg viewBox="0 0 24 24" fill="#d4b077" stroke="none"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg><span>4.8 (11)</span></div>
        </div>
      </div> -->

    </div>

  </div>

  <div class="divider-orn">
    <svg viewBox="0 0 24 24" fill="var(--gold)"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg>
  </div>

  <div class="foot-note">Resinoir · peças em resina artesanal</div>

  <!-- BOTTOM NAV -->
  <div class="bottomnav">
    <div class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg>
      <span>Início</span>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg>
      <span>Coleções</span>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
      <span>Pesquisar</span>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>
      <span>Catálogo completo</span>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><rect x="13" y="13" width="7" height="7"/></svg>
      <span>Categorias</span>
    </div>
  </div>

</div>

<script>
  const field = document.getElementById('starsField');
  const count = 55;
  for(let i=0;i<count;i++){
    const s = document.createElement('div');
    s.className = 'star';
    s.style.top = Math.random()*100+'%';
    s.style.left = Math.random()*100+'%';
    s.style.animationDelay = (Math.random()*3.2)+'s';
    s.style.width = s.style.height = (Math.random()*1.5+1)+'px';
    field.appendChild(s);
  }

  function spawnShootingStar(){
    const s = document.createElement('div');
    s.className = 'shooting-star';
    s.style.top = (10 + Math.random()*40) + '%';
    s.style.left = (55 + Math.random()*35) + '%';
    s.style.animation = 'shoot 1.1s ease-out forwards';
    field.appendChild(s);
    setTimeout(() => s.remove(), 1300);
  }

  function scheduleShootingStar(){
    const delay = 3500 + Math.random()*6000; // a cada ~3.5 a 9.5s
    setTimeout(() => {
      spawnShootingStar();
      scheduleShootingStar();
    }, delay);
  }
  scheduleShootingStar();
</script>

</body>
</html>

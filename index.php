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
      <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </div>
    <div class="wordmark">Resinoir</div>
    <div class="side">
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
      </div>
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>
      </div>
    </div>
  </navbar>

  <!-- HEADER -->
  <div class="header">
    <div class="eyebrow">Coleção</div>
    <h1>Nossas<br>categorias</h1>
    <div class="sub">peças autorais em resina, moldadas à mão e inspiradas na arquitetura gótica</div>
  </div>

  <!-- SELO ESTRELADO NOTURNO -->
  <div class="night-seal">
    <div class="stars" id="starsField"></div>
    <div class="seal">
      <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-bright)" stroke-width="1">
        <path d="M12 3a7 7 0 1 0 7 9 6 6 0 0 1-7-9z" fill="var(--gold-bright)" stroke="none"/>
      </svg>
    </div>
    <div class="seal-caption">feito à luz da lua</div>
  </div>

  <!-- BANNER DE COLECAO -->
  <div class="banner-section">
    <div class="banner-frame">
      <svg class="rose" viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="0.6">
        <circle cx="12" cy="12" r="9"/>
        <circle cx="12" cy="12" r="5"/>
        <path d="M12 3v18M3 12h18M5.5 5.5l13 13M18.5 5.5l-13 13"/>
      </svg>
      <div class="banner-text">
        <div class="banner-eyebrow">Coleção em destaque</div>
        <div class="banner-title">Vitral Sombrio</div>
        <div class="banner-sub">peças em resina inspiradas em rosáceas góticas</div>
        <div class="banner-cta">Ver coleção <span>&rarr;</span></div>
      </div>
    </div>

    <div class="cat-row">
      <div class="cat-btn">
        <div class="cat-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1.2"><path d="M12 2C9 6 7 9 7 12a5 5 0 0 0 10 0c0-3-2-6-5-10z"/><circle cx="12" cy="20" r="1.4"/></svg>
        </div>
        <div class="cat-label">Brincos</div>
      </div>
      <div class="cat-btn">
        <div class="cat-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1.2"><circle cx="12" cy="8" r="4"/><path d="M9 11 6 21h12l-3-10"/></svg>
        </div>
        <div class="cat-label">Colares</div>
      </div>
      <div class="cat-btn">
        <div class="cat-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1.2"><circle cx="12" cy="14" r="6"/><path d="M9 9l1.5-5h3L15 9"/></svg>
        </div>
        <div class="cat-label">Anéis</div>
      </div>
      <div class="cat-btn">
        <div class="cat-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1.2"><path d="M4 12a8 4 0 0 0 16 0 8 4 0 0 0-16 0z"/></svg>
        </div>
        <div class="cat-label">Pulseiras</div>
      </div>
      <div class="cat-btn">
        <div class="cat-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1.2"><path d="M12 2 3 21l9-4 9 4z"/></svg>
        </div>
        <div class="cat-label">Broches</div>
      </div>
    </div>
  </div>

  <!-- NOVIDADES EM VITRAIS -->
  <div class="divider-orn">
    <svg viewBox="0 0 24 24" fill="var(--gold)"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg>
  </div>
  <div class="novidades-section">
    <div class="eyebrow">Novidades</div>
    <div class="vitral-row">

      <div class="vitral-card">
        <div class="vitral-frame">
          <div class="mini-ceu"></div>
          <img class="img-card-vitral" src="./assets/imgs/WhatsApp Image 2026-07-16 at 13.52.51.jpeg" alt="">
        </div>
        <div class="vitral-caption">
          <div class="name">Brinco Morcego Lunar</div>
          <div class="price">R$ 89,00</div>
        </div>
      </div>

      <div class="vitral-card">
        <div class="vitral-frame">
          <div class="mini-ceu"></div>
          <img class="img-card-vitral" src="./assets/imgs/WhatsApp Image 2026-07-16 at 19.56.00.jpeg" alt="">
        </div>
        <div class="vitral-caption">
          <div class="name">Colar Rosácea</div>
          <div class="price">R$ 129,00</div>
        </div>
      </div>

      <div class="vitral-card">
        <div class="vitral-frame">
          <div class="mini-ceu"></div>
          <img class="img-card-vitral" src="./assets/imgs/WhatsApp Image 2026-07-13 at 21.22.27.jpeg" alt="">
        </div>
        <div class="vitral-caption">
          <div class="name">Anel Trevo Sombrio</div>
          <div class="price">R$ 69,00</div>
        </div>
      </div>

    </div>
  </div>

  <!-- CATALOGO GERAL -->
  <div class="catalog-section">
    <div class="eyebrow">Catálogo</div>
    <div class="product-grid">

      <?php
        $Listar = new ListController;
        $Listar = $Listar->listProdutos();
      ?>

      <div class="product-card">
        <div class="product-photo">
          <div class="product-badge">Novo</div>
          <div class="wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg></div>
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1"><path d="M12 2C9 6 7 9 7 12a5 5 0 0 0 10 0c0-3-2-6-5-10z"/></svg>
        </div>
        <div class="product-info">
          <div class="name">Brinco Lua Minguante</div>
          <div class="price">R$ 74,00</div>
          <div class="rating"><svg viewBox="0 0 24 24" fill="#d4b077" stroke="none"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg><span>4.9 (18)</span></div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-photo">
          <div class="wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg></div>
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/></svg>
        </div>
        <div class="product-info">
          <div class="name">Pingente Vitral Rubi</div>
          <div class="price">R$ 118,00</div>
          <div class="rating"><svg viewBox="0 0 24 24" fill="#d4b077" stroke="none"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg><span>5.0 (7)</span></div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-photo">
          <div class="wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg></div>
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1"><circle cx="12" cy="14" r="6"/><path d="M9 9l1.5-5h3L15 9"/></svg>
        </div>
        <div class="product-info">
          <div class="name">Anel Gótico Prata</div>
          <div class="price">R$ 96,00</div>
          <div class="rating"><svg viewBox="0 0 24 24" fill="#d4b077" stroke="none"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg><span>4.7 (24)</span></div>
        </div>
      </div>

      <div class="product-card">
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
      </div>

      <div class="product-card">
        <div class="product-photo">
          <div class="wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg></div>
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1"><path d="M12 6c-2-3-6-3-6 1 0 2 2 3 6 6 4-3 6-4 6-6 0-4-4-4-6-1z"/></svg>
        </div>
        <div class="product-info">
          <div class="name">Brinco Morcego Duplo</div>
          <div class="price">R$ 92,00</div>
          <div class="rating"><svg viewBox="0 0 24 24" fill="#d4b077" stroke="none"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg><span>4.9 (31)</span></div>
        </div>
      </div>

      <div class="product-card">
        <div class="product-photo">
          <div class="product-badge sold-out">Esgotado</div>
          <div class="wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg></div>
          <svg viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1"><path d="M4 12a8 4 0 0 0 16 0 8 4 0 0 0-16 0z"/></svg>
        </div>
        <div class="product-info">
          <div class="name">Pulseira Corrente Sombria</div>
          <div class="price">R$ 105,00</div>
          <div class="rating"><svg viewBox="0 0 24 24" fill="#d4b077" stroke="none"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17l-6.1 3.5 1.5-6.8-5.2-4.7 6.9-.7z"/></svg><span>4.6 (9)</span></div>
        </div>
      </div>

    </div>

    <button class="cta-btn">Ver catálogo completo</button>
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
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><rect x="13" y="13" width="7" height="7"/></svg>
      <span>Categorias</span>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 21s-7-4.4-9.5-8.8C.6 8.6 2.6 5 6.2 5 8.4 5 10 6.2 12 8c2-1.8 3.6-3 5.8-3 3.6 0 5.6 3.6 3.7 7.2C19 16.6 12 21 12 21z"/></svg>
      <span>Favoritos</span>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>
      <span>Sacola</span>
      <div class="badge">2</div>
    </div>
    <div class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
      <span>Perfil</span>
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

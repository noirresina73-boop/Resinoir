<?php
include 'autoloader.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Resinoir — Pesquisar</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<div class="device">

  <!-- BARRA DE PESQUISA -->
  <div class="search-bar">
    <div class="icon-btn" onclick="window.history.back()">
        <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M15 18l-6-6 6-6"/></svg>
    </div>
    <input type="text" id="searchInput" placeholder="Buscar peças, coleções..." autocomplete="off">
  </div>

  <div class="search-status" id="searchStatus"></div>

<div class="catalog-section">
  <div class="product-grid" id="pinterestGrid"></div>
</div>

  <!-- BOTTOM NAV -->
  <div class="bottomnav">
    <div class="nav-item" onclick="window.location.href='./index.php'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg>
      <span>Início</span>
    </div>
    <div class="nav-item" onclick="window.location.href='./colecao.php'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/></svg>
      <span>Coleções</span>
    </div>
    <div class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
      <span>Pesquisar</span>
    </div>
    <div class="nav-item" onclick="window.location.href='./catalogo.php'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 6v14"/><path d="M5 4c3 0 5.5 1 7 2.5C13.5 5 16 4 19 4v14c-3 0-5.5 1-7 2.5C10.5 19 8 18 5 18V4z"/></svg>
      <span>Catálogo</span>
    </div>
    <div class="nav-item" onclick="window.location.href='./categoria.php'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><rect x="13" y="13" width="7" height="7"/></svg>
      <span>Categorias</span>
    </div>
  </div>

</div>

<script>
const input = document.getElementById('searchInput');
const grid = document.getElementById('pinterestGrid');
const status = document.getElementById('searchStatus');

let debounceTimer;
let controller;

buscar('');

input.addEventListener('input', () => {
  clearTimeout(debounceTimer);
  const termo = input.value.trim();
  debounceTimer = setTimeout(() => buscar(termo), 350);
});

function renderizarGrid(produtos) {
  produtos.forEach(p => {
    const item = document.createElement('div');
    item.className = 'product-card';
    item.onclick = () => location.href = 'infos.php?id=' + p.id;
    item.innerHTML = `
      <div class="product-photo">
        <img class="img-card" src="${p.capa}" alt="${p.nome}">
      </div>
      <div class="product-info">
        <div class="name">${p.nome}</div>
        <div class="price">R$ ${p.valor}</div>
      </div>
    `;
    grid.appendChild(item);
  });
}

async function buscar(termo) {
  if (controller) controller.abort();
  controller = new AbortController();

  status.innerHTML = termo === '' ? 'Sugestões pra você' : 'Buscando...';
  grid.innerHTML = '';

  try {
    const resp = await fetch('./api/pesquisa.php?q=' + encodeURIComponent(termo), {
      signal: controller.signal
    });
    const dados = await resp.json();

    if (dados.resultados.length === 0) {
      status.innerHTML = termo === ''
        ? 'Nenhum produto encontrado'
        : `Nenhum resultado para "${termo}"`;
      return;
    }

    if (dados.termoSugerido) {
      status.innerHTML = `Nenhum resultado para "${termo}"<br><span class="search-suggestion">mas encontramos para "${dados.termoSugerido}"</span>`;
    } else {
      status.innerHTML = termo === '' ? 'Sugestões pra você' : '';
    }

    renderizarGrid(dados.resultados);
  } catch (e) {
    if (e.name !== 'AbortError') {
      status.innerHTML = 'Erro ao buscar.';
    }
  }
}
</script>

</body>
</html>
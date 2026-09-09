# Ícones SVG Customizáveis em Categorias & Coleções (Admin) — Implementation Plan

## Repository Research

**O que existe hoje:**
- **Tabelas**: `categoria` e `colecao` (schema em [schema.sql:L6-L26](file:///c:/xampp/htdocs/Resinoir/database/schema.sql#L6-L26)) já têm coluna `capa VARCHAR(255) NULL` para armazenar PATH/URL de imagem enviada via upload.
- **Heurística de ícones**: Em [ListController.php:L237-L257](file:///c:/xampp/htdocs/Resinoir/Controllers/ListController.php#L237-L257) existe `iconeCategoria($nome)` — 5 SVGs hardcoded (brinco, colar, broche, anel, pulseira) + 1 fallback (estrela 8 pontas). Hoje o ícone é ESCOLHIDO AUTOMATICAMENTE pelo NOME da categoria (ex: se nome contém "brinco", usa SVG brinco). O usuário NÃO TEM CONTROLE.
- **Estilo dos SVGs atuais**: `viewBox="0 0 24 24" fill="none" stroke="#d4b077" stroke-width="1.2"` — linha fina dourada, estética gótica/minimal. Coincide com `var(--gold-bright)`.
- **Cadastro admin**: [categorias-lista.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/categorias-lista.php) e [colecoes-lista.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/colecoes-lista.php) têm modal Bootstrap com campos Nome, Descrição, **Upload de Capa** (`<input type=file>`) + Destaque (apenas coleção). NÃO há alternativa ao upload.
- **APIs**: [categoria.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/api/categoria.php) e [colecao.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/api/colecao.php) POSTam apenas `acao/nome/descricao/capa(upload)/destaque`. `CatalogoAuxController` salva `capa` como string path.
- **Renderização pública da capa**:
  - Banner coleção em destaque: `<img class='img-card' src='$capa'>` — [ListController.php:L204](file:///c:/xampp/htdocs/Resinoir/Controllers/ListController.php#L204)
  - Cards de categoria (listCategoria): `<img class='img-card' src='$capa'>` — [ListController.php:L434](file:///c:/xampp/htdocs/Resinoir/Controllers/ListController.php#L434)
  - Cards de coleção (listColecao): mesmo padrão `.product-card > .product-photo > img.img-card`
  - Botões circulares categoria: `.cat-circle svg` — renderiza o heurístico `iconeCategoria`, NUNCA a capa salva. Ou seja, hoje capa e ícone são coisas separadas: capa é img grande, ícone é derivado do nome.
- **Listagem admin**: Capa exibida como `<img class=card-thumb src=...>` ou `—` quando vazio. Não tem suporte a SVG inline.

**Decisão de arquitetura (baixo risco): REUTILIZAR coluna `capa` existente, SEM ALTERAR SCHEMA.**  
Regra: se o valor de `capa` COMEÇAR com `<svg` (ou prefixo `svg:<svg` para distinção clara), interpretamos como **SVG inline**; caso contrário, **path/URL de imagem** (comportamento legado).

Motivos:
- Evita migration `ALTER TABLE` no InfinityFree (risco baixo, sem downtime).
- `VARCHAR(255)` comporta SVGs pequenos (~300–700 chars / 255 bytes é pouco — **ATENÇÃO**: SVGs longos estourarão 255 chars. Solução: usar prefixo + CHAVE do ícone ao invés do SVG inline).

**Decisão REFINADA (armazenamento):** em vez de gravar SVG inline em `capa`, gravamos uma **CHAVE curta** tipo `svg:anel` ou `svg:cruz-gotica` (≤ 20 chars, perfeito para VARCHAR 255). No render fazemos lookup via biblioteca. Vantagens: (1) VARCHAR(255) suficiente; (2) mudar um SVG depois = atualizar 1 lugar (biblioteca); (3) sem XSS vindo de string HTML salva. → **ESTRATÉGIA ADOTADA.**

## Files and Modules

### Arquivos a criar (1)
- `Gerenciar/styles/iconesBiblioteca.js` (ou inline `<script>` direto nos 2 arquivos PHP admin — inline para evitar arquivo novo; preferimos inline `<script>`)

### Arquivos a alterar (8)
1. **[Gerenciar/categorias-lista.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/categorias-lista.php)** — modal (grid ícones, input hidden, limpar), listagem (render SVG via chave), JS (`abrirModalCriar/Editar/salvar`, biblioteca SVG).
2. **[Gerenciar/colecoes-lista.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/colecoes-lista.php)** — IDENTICO ao acima (mesma estrutura de modal), mais campo destaque existente.
3. **[Gerenciar/api/categoria.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/api/categoria.php)** — aceitar `$_POST['icone_svg']` (chave tipo `svg:anel`) + `$_POST['remover_capa']`; mesclar com upload.
4. **[Gerenciar/api/colecao.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/api/colecao.php)** — mesmo do acima + destaque.
5. **[Gerenciar/styles/gerenciar.css](file:///c:/xampp/htdocs/Resinoir/Gerenciar/styles/gerenciar.css)** — estilos do `.svg-picker-grid`, `.svg-option` (thumbnail selecionável, borda dourada no selected), `.svg-thumb-listagem`.
6. **[Controllers/ListController.php](file:///c:/xampp/htdocs/Resinoir/Controllers/ListController.php)** —
   - Adicionar biblioteca central `public static $ICONE_BIBLIOTECA` (lookup chave → SVG string).
   - Helper `public static function renderizarCapaCategoriaColecao($capa, $modo = 'img-card' | 'svg-cat-circle' | 'thumb')`.
   - Refatorar `montaCatBtn` / `iconeCategoria`: **primeiro** tenta pegar chave `svg:` da `capa` do banco; senão cai na heurística por nome; senão fallback estrela.
   - Substituir `<img class='img-card' src='$capa'>` nos pontos L204, L434, e cards coleção pelo helper.
7. **[database/schema.sql](file:///c:/xampp/htdocs/Resinoir/database/schema.sql)** — adicionar comentário/documentação no bloco ALTER TABLE IF NOT EXISTS sobre o uso de `capa` como chave `svg:xxx` (para referência futura; sem ALTER real).
8. **[Gerenciar/Controllers/CatalogoAuxController.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/Controllers/CatalogoAuxController.php)** — métodos `atualizarCategoria/atualizarColecao` hoje só limpam `capa` quando arquivo é enviado; precisam suportar "definir capa com string chave" e "remover capa completamente" (quando `$_POST['remover_capa'] = 1` ou quando `$_POST['icone_svg']` vazio e upload vazio → manter existente, a menos que usuário peça remoção). APIs categoria.php/colecao.php passam `$capa` como string ou `null` ("remover") ou string vazia não troca.

## Implementation Steps (Ordem de dependência)

### PASSO 1 — Biblioteca central de SVGs em ListController (base de tudo)
- Adicionar `const ICONE_BIBLIOTECA` em [ListController.php](file:///c:/xampp/htdocs/Resinoir/Controllers/ListController.php) — array associativo `chave => ['nome' => 'Visível', 'svg' => '<svg...>']`. Estilo uniforme: stroke `#d4b077` (ou `currentColor` para CSS flexível), sw=1.2, viewBox 24×24.
- **Chaves e SVGs a criar (16 ícones — gótico / joalheria):**
  1. `colar` — existente
  2. `brinco` — existente
  3. `broche` — existente
  4. `anel` — existente
  5. `pulseira` — existente
  6. `pingente` (colar com pingente detalhado)
  7. `cruz` (cruz gótica orna)
  8. `estrela` (estrela 8 pontas, já usada como fallback hoje)
  9. `lua` (lua crescente orna)
  10. `coroa` (tiara/coroa pequena — resina)
  11. `rosa` (rosa gótica / flor)
  12. `coracao` (coração ornamental gótico)
  13. `chave` (chave antiga)
  14. `ampulheta` (ampulheta ornamental)
  15. `caveira` (caveira gótica minimal)
  16. `rosario` (contas / grãos)
- Helper `public static function pegarSvgPorChave($chave)`: valida prefixo `svg:` → lookup → retorna SVG string ou `null`.
- Helper `public static function capaParaHtml($capa, $classeWrapper = '', $classeSvg = '')`:
  - Retorna `<img src=...>` se path/URL
  - Retorna `<div class="svg-img-card $classeWrapper"> ...svg inline... </div>` se chave `svg:`
  - Retorna `''` se vazio

### PASSO 2 — Ajustar CRUD backend (APIs + CatalogoAuxController)
- **CatalogoAuxController**:
  - `atualizarCategoria` / `atualizarColecao`: **hoje** só troca `capa` se `$capa !== null` (upload). Precisamos permitir também **setar explicitamente $capa como string (chave svg:xxx)** OU **limpar capa ($capa = '' → definir NULL)**. Já suporta string, basta as APIs passarem. OK, nenhum código alterado aqui.
- **[Gerenciar/api/categoria.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/api/categoria.php)**:
  - Ler `$_POST['icone_svg']` (chave validada: `^svg:[a-z0-9-]+$` via preg_match; senão descarta) e `$_POST['limpar_capa']` (flag 1/0).
  - Regra de merge para $capa final:
    1. Se **tem upload de arquivo** → usa upload (sobrescreve tudo).
    2. Senão se `limpar_capa === '1'` → $capa = `''` (null no banco).
    3. Senão se tem `icone_svg` válido → $capa = `icone_svg` (a chave `svg:...`).
    4. Senão → $capa = `null` (**não** altera a capa existente; comportamento atual edição).
  - Em `criarCategoria`: se sem upload + sem icone_svg → `capa` default vazia (igual hoje).
- **[Gerenciar/api/colecao.php](file:///c:/xampp/htdocs/Resinoir/Gerenciar/api/colecao.php)**: EXATAMENTE mesmas regras acima.
- **Segurança**: validar a chave `icone_svg` SEMPRE contra array_keys(ListController::$ICONE_BIBLIOTECA) ou regex para impedir inserção de HTML arbitrário. Como gravamos só `svg:anel` curto, XSS é impossível.

### PASSO 3 — Refatorar render pública (ListController)
- **`iconeCategoria($nome, $capaDoBanco = null)`**: modificar assinatura. Primeiro, se $capaDoBanco começa com `svg:` → retorna SVG da biblioteca; senão heurística nome atual; senão fallback.
- **`montaCatBtn`**: passar `$cat['capa']` para `iconeCategoria` (já tem o array $cat no loop).
- **Banner coleção (L204)**: trocar `<img class='img-card' src='$capa'>` por helper `capaParaHtml($capa, estilos para encaixar no banner-frame 100% width/height object-fit)`. SVGs precisam de `width:100%; height:100%` + `background` igual ao `.product-photo` (linear-gradiente gótico).
- **Cards de categoria em listCategoria (L434)**: mesma troca img → helper.
- **Cards de coleção em listColecao**: mesma troca img → helper.
- **CSS adicional em style.css**: `.svg-img-card` — wrapper que replica `.product-photo` background gótico, centraliza SVG, aplica padding para o SVG não ficar enorme:
  ```
  .svg-img-card{
    width:100%; height:100%;
    background:linear-gradient(160deg, #201118 0%, #120a0e 60%, #090506 100%);
    display:flex; align-items:center; justify-content:center;
    padding:16%;
    border:1px solid rgba(176,141,87,0.25);
  }
  .svg-img-card svg{ width:100%; height:100%; max-width:200px; max-height:200px; stroke:var(--gold-bright); stroke-width:1; }
  ```

### PASSO 4 — Telas admin (categorias-lista.php + colecoes-lista.php)
- **Modal Criar/Editar**:
  - Inserir SEÇÃO ABAIXO do input "Capa" e antes do checkbox destaque (coleção) / erroItem.
  - Estrutura:
    ```html
    <div class="mb-3">
      <label class="form-label">Ou escolha um ícone SVG <span class="text-secondary small">(usado se não enviar capa)</span></label>
      <div id="svgPicker" class="svg-picker-grid"></div>
      <input type="hidden" id="iconeSvgEscolhido" value="">
      <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btnLimparIcone">Limpar ícone escolhido</button>
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="limparCapaCompleta">
        <label class="form-check-label small" for="limparCapaCompleta">Remover capa/ícone atual</label>
      </div>
    </div>
    ```
  - Injetar script inline com `const BIBLIOTECA_SVG` (mesmas 16 chaves/nomes/SVGs). Renderizar `.svg-picker-grid` no onload. Cada `.svg-option`: `data-chave="svg:colar"`, `<div class="svg-thumb"><?=svg?></div><label>Colar</label>`. Clique: toggle `.selected` + atualizar hidden `#iconeSvgEscolhido`.
  - **JS `abrirModalCriar`**: limpar selected, zera hidden, zera limparCapaCompleta.
  - **JS `abrirModalEditar(cat)`**: se `cat.capa` começa com `svg:`, acha `.svg-option[data-chave=...]`, adiciona `.selected`, preenche hidden.
  - **JS `salvar()`**: adicionar ao FormData: `icone_svg` (hidden) + `limpar_capa` (checkbox).
- **Listagem `<table>`**: na coluna "Capa", trocar condição `if ($c['capa'])` para:
  - Se `$capa` começa com `svg:` → renderiza `<div class="svg-thumb-admin"><?=ListController::pegarSvgPorChave($capa)?></div>`
  - Senão → `<img>` como hoje
  - Else → `—`

### PASSO 5 — Estilos admin em [Gerenciar/styles/gerenciar.css](file:///c:/xampp/htdocs/Resinoir/Gerenciar/styles/gerenciar.css)
```
.svg-picker-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(62px,1fr));
  gap:10px;
  padding:10px;
  background:#141013;
  border:1px solid rgba(176,141,87,0.2);
  border-radius:8px;
  max-height:280px;
  overflow-y:auto;
}
.svg-option{
  cursor:pointer;
  border:1px solid rgba(233,224,201,0.12);
  border-radius:8px;
  padding:8px 4px 6px;
  display:flex; flex-direction:column; align-items:center; gap:4px;
  background:#1a1317;
  transition:all .15s ease;
}
.svg-option:hover{ border-color:rgba(176,141,87,0.5); transform:translateY(-1px); }
.svg-option.selected{
  border-color:#d4b077;
  background:linear-gradient(180deg, rgba(212,176,119,0.12), rgba(124,31,46,0.12));
  box-shadow:0 0 0 1px rgba(212,176,119,0.6);
}
.svg-option .svg-thumb{
  width:34px; height:34px;
  display:flex; align-items:center; justify-content:center;
}
.svg-option .svg-thumb svg{ width:100%; height:100%; stroke:#d4b077; }
.svg-option label{
  font-size:10px;
  color:#a89f8b;
  letter-spacing:0.6px;
  text-transform:uppercase;
}
/* listagem */
.svg-thumb-admin{
  width:48px; height:48px;
  border-radius:6px;
  background:#1a1317;
  border:1px solid rgba(176,141,87,0.22);
  display:flex; align-items:center; justify-content:center;
}
.svg-thumb-admin svg{ width:26px; height:26px; stroke:#d4b077; }
.card-thumb{ width:48px; height:48px; border-radius:6px; object-fit:cover; }
```

### PASSO 6 — database/schema.sql (anotação apenas)
Adicionar comentário `--` após CREATE TABLE categoria explicando o uso de `capa` como path OU `svg:chave`. Sem ALTER real.

## Dependencies and Considerations
- **Sem migrations / sem colunas novas**: Zero impacto em InfinityFree; uso de prefixo `svg:` + biblioteca garante compatibilidade retroativa.
- **Heurística de nome preservada como fallback**: Categorias/coleções antigas sem `svg:` na capa continuam exibindo ícone por nome (brinco/colar/etc).
- **Prioridade**: Upload de imagem > Ícone SVG escolhido > (edição) manter capa antiga.
- **XSS seguro**: nunca renderizamos SVG salvo direto do banco; sempre fazemos lookup por chave contra biblioteca. HTML do SVG é **hardcoded const PHP/JS**.
- **Carregamento admin**: Bootstrap 5 já presente; grid SVG picker usa CSS vanilla.
- **Performance**: Biblioteca tem 16 SVGs pequenos (~10kb total), zero impacto.
- **Compatibilidade mobile do admin**: Grid `auto-fill minmax(62px)` funciona bem em telas estreitas. Modal Bootstrap já responsive.
- **Estética gótica**: Todos os novos SVGs seguem o padrão stroke 1.2 gold #d4b077; wrappers `.svg-img-card` reutilizam gradiente gótico existente em `.product-photo`.

## Validation
1. **Unit backend manual**:
   - Em API categoria, POST `acao=criar&nome=Teste&descricao=&icone_svg=svg:lua` → verificar banco: `capa = 'svg:lua'`.
   - POSTar edição com `limpar_capa=1` → `capa = NULL` no banco.
   - POSTar upload de imagem junto com `icone_svg` → imagem vence, capa = path.
   - `icone_svg=svg:chave_invalida_xxx` → deve ser REJEITADO (não está na biblioteca).
2. **UI admin (categorias e coleções, ambos)**:
   - Abrir modal criar → clicar em "Lua" → borda dourada seleciona, hidden atualiza. Salvar → listagem mostra SVG thumb (lua).
   - Abrir modal editar → ícone já selecionado vem marcado. Trocar para "Cruz" → salvar → atualiza.
   - Marcar "Remover capa/ícone atual" → salvar → coluna mostra `—`.
   - Upload de arquivo sem limpar → capa = imagem (sobrescreve SVG).
3. **Site público (render)**:
   - Botão circular categoria (home, embaixo do banner coleção) → se categoria tem `svg:cruz` → renderiza SVG cruz ao invés de heurística nome.
   - Card de categoria em categoria.php e coleção em colecao.php → wrapper `.svg-img-card` com gradiente gótico + SVG centralizado (sem `<img>` quebrado).
   - Banner coleção em destaque → SVG grande centralizado com background gótico, em vez de `<img src=svg:xxx>`.
4. **Regression test**:
   - Categorias/coleções existentes SEM `svg:` (com imagem real) continuam mostrando `<img>` normalmente.
   - Categoria sem capa e sem imagem → continua fallback heurística (se nome contém brinco → brinco.svg).
5. **Visual**: devTools inspecionar `.svg-img-card`, `.svg-thumb-admin`, `.svg-option.selected` confirmar CSS aplicado corretamente, sem cores quebradas (sem #000 preto acidental, sempre stroke gold).

## Risks
- **Risco 1:** VARCHAR(255) MUITO PEQUENO caso alguém salve SVG inline → mitigado com estratégia CHAVE (`svg:anel` ≤ 16 chars).
- **Risco 2:** Lookup iconeCategoria esquecer de passar $capa → botões circulares não pegam SVG escolhido → mitigado: refatorar assinatura e passar dado no loop `mostraColecaoNova` (já tem $cat completo).
- **Risco 3:** `categoria.php` / `colecao.php` chamam `listCategoria('catalogo')` com JOIN SQL — esse SELECT precisa incluir a coluna `capa` da categoria/coleção. Verificar queries.
  *Mitigação:* Ler métodos `listCategoria` e `listColecao` antes de implementar; adicionar `ca.capa` ao SELECT se não tiver.
- **Risco 4:** Admin: passar `acao=definirDestaque` em coleção.php sem nome — hoje o backend ignora capa quando `$capa=null` (ok). Continuar passando null para capa nesse fluxo (não adicionar icone_svg acidentalmente).
- **Risco 5:** SVGs novos serem criados com `stroke="currentColor"` sem fallback no wrapper → em botões `.cat-circle svg` stroke já é setado via CSS (bom!), porém em `.svg-img-card svg` precisamos garantir `stroke:var(--gold-bright)` → adicionado no CSS do passo 3.
- **Risco 6:** Admin JS `abrirModalEditar(categoria)` pode não receber `capa` no json_encode se SQL não selecionar `capa` → hoje o SELECT em listarCategorias é `SELECT id, nome, descricao, capa FROM categoria` (OK).

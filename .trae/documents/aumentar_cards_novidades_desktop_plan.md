# Aumentar Cards de Novidades no Desktop — Implementation Plan

## Repository Research

**Contexto atual:**
- A home [index.php](file:///c:/xampp/htdocs/Resinoir/index.php#L63-L79) renderiza 3 cards de novidades via `ListController::listNovidadesVitral(3)` dentro de `.novidades-section > .vitral-row`.
- Cada card é `.vitral-card` → contém `.vitral-frame` (imagem clip-path arco gótico `#gothicArch`) + `.vitral-caption` (nome + preço).
- O CSS é **mobile-first**: os estilos-base em [style.css](file:///c:/xampp/htdocs/Resinoir/styles/style.css#L340-L418) aplicam-se a TODAS as telas. Há apenas 1 media query — `@media (max-width: 460px)` em [style.css:791](file:///c:/xampp/htdocs/Resinoir/styles/style.css#L791) — que reduz tamanhos para telas *ainda menores* (celulares pequenos).
- **Não existe media query para DESKTOP.** Atualmente desktop usa os mesmos valores mobile:
  - `.vitral-card`: `width: 170px`
  - `.vitral-frame`: `width: 150px` (aspect-ratio 3/4 → 150×200px)
  - Gap entre cards: `14px`
  - Nome: `17px`, Preço: `11px`
- O usuário quer que no **desktop** os cards fiquem MAIORES, enquanto no **mobile permaneçam exatamente como estão**.

**Arquitetura CSS:**
- Breakpoint existente é `460px` como mobile-pequeno.
- Para desktop, usaremos `min-width: 768px` (tablet+) e opcionalmente outro salto em `min-width: 1200px` (desktop grande).
- O layout `.vitral-row` tem `overflow-x:auto` no mobile (scroll horizontal). No desktop podemos desativar scroll e usar `justify-content:center` para centralizar os 3 cards (que já cabem lado a lado).

## Files and Modules
- `styles/style.css`: Único arquivo a ser alterado. Adicionar **um novo bloco `@media (min-width: 768px)`** com overrides para `.novidades-section`, `.vitral-row`, `.vitral-card`, `.vitral-frame`, `.vitral-caption .name/price`, `.tag-esgotado`. Nenhum arquivo PHP é tocado.

## Implementation Steps

1. **Adicionar media query `@media (min-width: 768px)` para tablet/desktop**
   - **Posição no arquivo**: Inserir DEPOIS do bloco mobile existente (aprox. linha 418, antes de `/* CATALOGO GRID */`), para respeitar cascata (regras posteriores com mesma especificidade sobrescrevem as base).
   - Sobrescrever apenas o que precisa mudar; manter o restante herdado das regras base.

2. **Ajustes de tamanho dentro do media query 768px+**
   - `.novidades-section`: padding mais generoso (ex: `48px 40px 16px` ao invés de `36px 0 8px 24px`).
   - `.vitral-row`: 
     - Remover `overflow-x:auto` → `overflow: visible` (não precisa de scroll horizontal).
     - Aumentar `gap` de `14px` para `32px`.
     - Aumentar `padding` horizontal.
     - Centralizar com `justify-content: center` (já tem na base, confirmar).
   - `.vitral-card`: aumentar `width` de `170px` para `240px` (tablet) / `280px` (desktop grande).
   - `.vitral-frame`: aumentar `width` de `150px` para **igualar** ao card (ou `220px` / `260px`) mantendo `aspect-ratio:3/4`. A imagem acompanha por ter `width/height:100% object-fit:cover`.
   - `.vitral-caption .name`: aumentar fonte de `17px` → `20px` (tablet) / `22px` (desktop).
   - `.vitral-caption .price`: aumentar fonte de `11px` → `13px`.
   - `.vitral-frame .tag-esgotado`: aumentar `font-size` e `padding` proporcionalmente.

3. **(Opcional) Segundo salto `@media (min-width: 1200px)`** para desktop large:
   - `.vitral-card`: `width: 300px`
   - `.vitral-frame`: `width: 280px`
   - `.vitral-caption .name`: `24px`
   - Gap: `40px`

4. **Corrigir CSS quebrado detectado** em [style.css:419-422](file:///c:/xampp/htdocs/Resinoir/styles/style.css#L419-L422): 4 linhas órfãs `color:var(--gold); margin-top:4px; letter-spacing:0.8px; }` duplicadas e fora de qualquer seletor — elas não causam erro de renderização mas estão poluindo; remover.

## Dependencies and Considerations
- Nenhuma dependência externa. CSS vanilla.
- `clip-path:url(#gothicArch)` está em inline SVG no `<head>` de `index.php` — ele usa `objectBoundingBox`, portanto escala automaticamente com o frame (não precisa ajustar).
- Manter **mobile inalterado**: todas as mudanças ficam DENTRO de `@media (min-width: 768px)`; as regras-base (fora de media query) permanecem idênticas.
- `.vitral-card` tem `width:170px` e `.vitral-frame` dentro tem `width:150px` (o card é 20px mais largo que o frame, criando offset). No desktop manter essa proporção: frame = card - 20px (ex: card 240 → frame 220).

## Validation
- **Mobile (< 460px / < 768px):** abrir página, confirmar que cards e scroll horizontal continuam IDÊNTICOS (largura, fonte, espaçamento, tag esgotado).
- **Tablet (768px–1199px):** redimensionar janela ~1024px, confirmar que cards são visivelmente maiores, sem scroll horizontal, gap maior, fontes caption maiores, tag-esgotado proporcional.
- **Desktop (≥1200px):** confirmar segundo salto de tamanho (se step 3 incluso).
- **Visual:** hover/click continua funcionando, clip-path gótico continua alinhado com imagem, tag "Novo/Esgotado" continua no canto inferior centralizado.
- **Cross-check:** inspecionar no DevTools os seletores `.vitral-card`, `.vitral-frame` para cada breakpoint e confirmar valores das propriedades.

## Risks
- **Risco 1:** Ordem errada do media query (inserir ANTES das regras-base) → sobrescrita reversa.  
  *Mitigação:* Inserir media query DEPOIS das regras base de vitral (linha ~418, antes de CATALOGO GRID) — CSS cascade garante que regras mais abaixo com mesmo @ ganhem.
- **Risco 2:** `.vitral-frame` ficar com `width` > `.vitral-card` (overflow).  
  *Mitigação:* Sempre frame = card - 20px (mesmo padrão mobile: 170/150).
- **Risco 3:** Remover `overflow-x:auto` afeta mobile se o media query estiver errado.  
  *Mitigação:* Garantir `overflow: visible` apenas dentro de `@media (min-width: 768px)`.
- **Risco 4:** A limpeza das 4 linhas órfãs (step 4) acidentalmente remover código válido.  
  *Mitigação:* Ler trecho exato [style.css:419-422](file:///c:/xampp/htdocs/Resinoir/styles/style.css#L419-L422) antes — são linhas soltas, sem seletor `{ }` aberto; remoção segura.

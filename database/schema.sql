USE `if0_42359254_resinoir`;

-- --------------------------------------------------------
-- Tabela de categorias
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  `data_criacao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `capa` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de coleções
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `colecao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  `data_criacao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `capa` VARCHAR(255) DEFAULT NULL,
  `destaque` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela principal de produtos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idPDR` VARCHAR(100) NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `modelo` VARCHAR(255) DEFAULT NULL,
  `descricao` TEXT DEFAULT NULL,
  `cor` VARCHAR(100) DEFAULT NULL,
  `tamanho` INT DEFAULT NULL,
  `estoque` INT NOT NULL DEFAULT 0,
  `categoria` INT DEFAULT NULL,
  `colecao` INT DEFAULT NULL,
  `imagem` JSON DEFAULT NULL,
  `encomenda` TINYINT(1) NOT NULL DEFAULT 0,
  `valor` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `custo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `totalVendidos` INT NOT NULL DEFAULT 0,
  `novidade` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('disponivel','esgotado','sob_encomenda') NOT NULL DEFAULT 'disponivel',
  `capa` VARCHAR(255) DEFAULT NULL,
  `data_criacao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_idPDR` (`idPDR`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_colecao` (`colecao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Configurações gerais do sistema
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `chave` VARCHAR(100) NOT NULL,
  `valor` TEXT NOT NULL,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuracoes` (`chave`, `valor`, `atualizado_em`)
VALUES
  ('preco_gasolina', '5.80', NOW()),
  ('cep_origem', '85506290', NOW())
ON DUPLICATE KEY UPDATE
  `valor` = VALUES(`valor`),
  `atualizado_em` = NOW();

-- --------------------------------------------------------
-- Vendas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cliente_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vendas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cliente` VARCHAR(255) NOT NULL DEFAULT '',
  `cliente_id` INT DEFAULT NULL,
  `produto_id` INT DEFAULT NULL,
  `produto_nome` VARCHAR(255) DEFAULT NULL,
  `quantidade` INT NOT NULL DEFAULT 1,
  `valor_unitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `acrescimo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `valor_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `custo_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `observacao` TEXT DEFAULT NULL,
  `data_venda` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vendas_data` (`data_venda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `venda_itens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `venda_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `produto_nome` VARCHAR(255) NOT NULL,
  `quantidade` INT NOT NULL DEFAULT 1,
  `valor_unitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `valor_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `custo_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_venda_itens_venda` (`venda_id`),
  KEY `idx_venda_itens_produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Ajustes para bancos que já existem
-- --------------------------------------------------------
ALTER TABLE `produtos`
  ADD COLUMN IF NOT EXISTS `custo` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor`;
ALTER TABLE `produtos`
  ADD COLUMN IF NOT EXISTS `status` ENUM('disponivel','esgotado','sob_encomenda') NOT NULL DEFAULT 'disponivel' AFTER `novidade`;

ALTER TABLE `vendas`
  ADD COLUMN IF NOT EXISTS `cliente` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`,
  ADD COLUMN IF NOT EXISTS `cliente_id` INT DEFAULT NULL AFTER `cliente`,
  ADD COLUMN IF NOT EXISTS `produto_id` INT DEFAULT NULL AFTER `cliente`,
  ADD COLUMN IF NOT EXISTS `produto_nome` VARCHAR(255) DEFAULT NULL AFTER `produto_id`,
  ADD COLUMN IF NOT EXISTS `quantidade` INT NOT NULL DEFAULT 1 AFTER `produto_nome`,
  ADD COLUMN IF NOT EXISTS `valor_unitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `quantidade`,
  ADD COLUMN IF NOT EXISTS `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor_unitario`,
  ADD COLUMN IF NOT EXISTS `acrescimo` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `desconto`,
  ADD COLUMN IF NOT EXISTS `valor_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `acrescimo`,
  ADD COLUMN IF NOT EXISTS `custo_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor_total`,
  ADD COLUMN IF NOT EXISTS `observacao` TEXT DEFAULT NULL AFTER `custo_total`,
  ADD COLUMN IF NOT EXISTS `data_venda` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `observacao`;

ALTER TABLE `venda_itens`
  ADD COLUMN IF NOT EXISTS `venda_id` INT NOT NULL DEFAULT 0 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `produto_id` INT NOT NULL DEFAULT 0 AFTER `venda_id`,
  ADD COLUMN IF NOT EXISTS `produto_nome` VARCHAR(255) NOT NULL DEFAULT '' AFTER `produto_id`,
  ADD COLUMN IF NOT EXISTS `quantidade` INT NOT NULL DEFAULT 1 AFTER `produto_nome`,
  ADD COLUMN IF NOT EXISTS `valor_unitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `quantidade`,
  ADD COLUMN IF NOT EXISTS `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor_unitario`,
  ADD COLUMN IF NOT EXISTS `valor_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor_unitario`,
  ADD COLUMN IF NOT EXISTS `custo_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor_total`;

-- --------------------------------------------------------
-- Como importar no InfinityFree:
-- 1) abra phpMyAdmin do painel
-- 2) escolha o banco if0_42359254_resinoir
-- 3) execute este arquivo SQL
-- --------------------------------------------------------

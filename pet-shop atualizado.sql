-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/05/2025 às 15:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `petshop_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id_agendamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_pet` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `id_funcionario` int(11) DEFAULT NULL,
  `data_hora` datetime NOT NULL,
  `status` varchar(30) DEFAULT 'pendente',
  `nome_pet` varchar(100) DEFAULT NULL,
  `nome_dono` varchar(100) DEFAULT NULL,
  `telefone` varchar(100) DEFAULT NULL,
  `servico_nome` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id_agendamento`, `id_cliente`, `id_pet`, `id_servico`, `id_funcionario`, `data_hora`, `status`, `nome_pet`, `nome_dono`, `telefone`, `servico_nome`, `observacoes`, `criado_em`, `atualizado_em`) VALUES
(3, 3, 1, 8, NULL, '2025-06-12 08:30:00', 'pendente', NULL, 'paulo', '81998876554', NULL, NULL, '2025-05-28 09:21:05', '2025-05-28 09:21:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_nascimento` date DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nome`, `email`, `senha`, `telefone`, `endereco`, `data_cadastro`, `data_nascimento`, `cpf`, `genero`) VALUES
(1, 'matheus ferreira da silva ', 'matheusgnr531@gmail.com', '$2y$10$YNsMxu81v0jq2NoLG2HTH.E6NxauTfTXcRu/aAJ.mzro3NgP18XHG', '81998876554', 'rua da soledade n°55', '2025-05-13 14:35:16', NULL, NULL, NULL),
(2, 'asafe emanuel', 'rikudousenin743@gmail.com', '$2y$10$joeFXGtIKfWfzr62km0Huejd44c8kQtuJsIBdRSX2NyRpaIvSap0a', '81987654321', 'av.matheus botelho', '2025-05-20 13:05:51', NULL, NULL, NULL),
(3, 'Paulo José dos Santos', 'paulosantos123@gmail.com', '$2y$10$i5CdOoSiEZpsz0ZXpwqPKu2GMX9JR0ksxVXzUcUoSh1NsBFS3Ar7i', '987786556', 'Rua Gomes taborda', '2025-05-27 14:44:51', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id_funcionario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id_funcionario`, `nome`, `cargo`, `email`, `telefone`, `data_nascimento`, `data_cadastro`, `senha`) VALUES
(3, 'Administrador', 'chefe', 'admin@petshop.com', '(81)996827136', NULL, '2025-05-27 10:06:59', '$2y$10$Ya8ZLsuFPgX1lsz0WW2Ufebr/9SIy/5x/1zbI65rLcgfMwD11KGhm'),
(4, 'AsafeGNR', NULL, 'rikudousenin743@gmail.com', NULL, NULL, '2025-05-27 10:06:59', '$2y$10$1FBGOU0zFW/pPmBbY8b54OMo9ReYhuYMq2JCtxtvm6ZqVdI85g58G');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id_item` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `data_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'em andamento',
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pets`
--

CREATE TABLE `pets` (
  `id_pet` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `especie` varchar(50) DEFAULT NULL,
  `raca` varchar(50) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pets`
--

INSERT INTO `pets` (`id_pet`, `nome`, `especie`, `raca`, `idade`, `id_cliente`) VALUES
(1, 'tapioca', NULL, NULL, NULL, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `nome`, `categoria`, `descricao`, `preco`, `imagem_url`) VALUES
(9, 'Ração Premium para Gatos', 'Alimentação', 'Ração com vitaminas e sabor peixe.', 79.90, 'imagens/racao_gato.jpg'),
(10, 'Bola com Guizo', 'Brinquedos', 'Brinquedo interativo para gatos.', 15.00, 'imagens/bola_guizo.jpg'),
(11, 'Condicionador Pet', 'Higiene', 'Deixa o pelo macio e com brilho.', 22.50, 'imagens/condicionador.jpg'),
(12, 'Arranhador de Papelão para Gatos', 'Brinquedos', 'Ajuda a manter as unhas dos gatos saudáveis e evita arranhões em móveis.', 59.90, 'imagens/arranhador.jpg'),
(13, 'Petisco Natural para Cães', 'Alimentação', 'Petisco saudável sem corantes e conservantes.', 18.50, 'imagens/petisco.jpg'),
(14, 'Areia Higiênica para Gatos', 'Higiene', 'Areia absorvente com controle de odor.', 27.90, 'imagens/areia.jpg'),
(15, 'Coleira Antipulgas', 'Acessórios', 'Proteção contra pulgas e carrapatos por até 8 meses.', 89.00, 'imagens/coleira_antipulgas.jpg'),
(16, 'Shampoo Medicinal Antipulgas', 'Higiene', 'Shampoo para tratamento de pulgas e coceiras.', 34.90, 'imagens/shampoo_antipulgas.jpg'),
(17, 'Bebedouro Automático', 'Acessórios', 'Garante água fresca para o pet o dia todo.', 74.90, 'imagens/bebedouro.jpg'),
(18, 'Bolinha Interativa com Luz', 'Brinquedos', 'Bolinha que pisca luz ao se mover, ideal para gatos.', 19.90, 'imagens/bolinha_luz.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso_servicos`
--

CREATE TABLE `progresso_servicos` (
  `id_progresso` int(11) NOT NULL,
  `id_agendamento` int(11) NOT NULL,
  `status_atual` varchar(50) DEFAULT 'pendente',
  `descricao_atividade` text DEFAULT NULL,
  `data_atualizacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id_servico` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `duracao_minutos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id_servico`, `nome`, `descricao`, `preco`, `duracao_minutos`) VALUES
(6, 'Banho', 'Banho completo com produtos específicos para pets.', 50.00, 30),
(7, 'Tosa', 'Tosa higiênica ou completa conforme solicitado.', 70.00, 45),
(8, 'Consulta Médica', 'Avaliação clínica com veterinário especializado.', 120.00, 60),
(9, 'Vacinação', 'Aplicação de vacinas obrigatórias e opcionais.', 90.00, 30),
(10, 'Banho e Tosa', 'Pacote completo de banho e tosa.', 110.00, 75);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id_agendamento`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_pet` (`id_pet`),
  ADD KEY `id_servico` (`id_servico`),
  ADD KEY `id_funcionario` (`id_funcionario`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id_funcionario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id_pet`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`);

--
-- Índices de tabela `progresso_servicos`
--
ALTER TABLE `progresso_servicos`
  ADD PRIMARY KEY (`id_progresso`),
  ADD KEY `id_agendamento` (`id_agendamento`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id_servico`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id_agendamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pets`
--
ALTER TABLE `pets`
  MODIFY `id_pet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `progresso_servicos`
--
ALTER TABLE `progresso_servicos`
  MODIFY `id_progresso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id_servico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`id_pet`) REFERENCES `pets` (`id_pet`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_3` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_4` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id_funcionario`) ON DELETE SET NULL;

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Restrições para tabelas `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Restrições para tabelas `progresso_servicos`
--
ALTER TABLE `progresso_servicos`
  ADD CONSTRAINT `progresso_servicos_ibfk_1` FOREIGN KEY (`id_agendamento`) REFERENCES `agendamentos` (`id_agendamento`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

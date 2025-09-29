-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/09/2025 às 05:10
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
-- Banco de dados: `skulljabb`
CREATE DATABASE IF NOT EXISTS `skulljabb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skulljabb`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao`
--

CREATE TABLE `avaliacao` (
  `ID_avaliacao` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `nota` tinyint(4) NOT NULL,
  `comentario` text DEFAULT NULL,
  `data_avaliacao` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacao`
--

INSERT INTO `avaliacao` (`ID_avaliacao`, `ID_cliente`, `ID_jogo`, `nota`, `comentario`, `data_avaliacao`) VALUES
(10, 4, 19, 4, 'Interessante', '2025-09-28 23:01:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `ID_carrinho` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinho`
--

INSERT INTO `carrinho` (`ID_carrinho`, `ID_cliente`, `ID_jogo`, `quantidade`) VALUES
(20, 3, 10, 1),
(19, 2, 44, 1),
(22, 3, 11, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `ID_cliente` int(11) NOT NULL,
  `email` varchar(70) NOT NULL,
  `CPF` varchar(14) NOT NULL,
  `nome` varchar(70) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `pontos` int(11) NOT NULL DEFAULT 50,
  `usuario` varchar(70) NOT NULL,
  `foto` varchar(255) NOT NULL DEFAULT '../../Img/Perfis/none.png',
  `moldura` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`ID_cliente`, `email`, `CPF`, `nome`, `senha`, `pontos`, `usuario`, `foto`, `moldura`) VALUES
(1, 'galdinob596@gmail.com', '501.988.228-85', 'Bea', '$2y$10$FfLDzgzMPTt10nkYi6VWOeXevDDlwdeVJ6IaoeiFpSZ70FpA1b63O', 50, 'Bellatrizx', '../../Img/Perfis/User 03.jpeg', '../../Img/Loja de Pontos/moldura_cogumelo.png'),
(2, 'jeeh_iero@yahoo.com.br', '123.456.789-12', 'dies', '$2y$10$m6NKIXxw/4nmaI/zEkYkfuZzXK4n/L6OJtWIbRriPPxvxuEZjf.9q', 50, 'dies', '../../Img/Perfis/none.png', ''),
(3, 'EstherGaldino@gmail.com', '212.252.365-78', 'Esther', '$2y$10$s4yp9GD50NlcBIi24y26IOFfclbqMyuPTG9oEyiRS3oEnD.SfIrb6', 50, 'steh', '../../Img/Perfis/Artist_.jpeg', '../../Img/Loja de Pontos/moldura_cogumelo.png'),
(4, 'Teste@gmail.com', '000.000.000-00', 'Beatriz', '$2y$10$cqp3sK5E5GrWUdVo3wgaH.wV2NNdWDgreVkjKzmrA9QVX43quElDe', 50, 'bea', '../../Img/Perfis/User 03.jpeg', '../../Img/Loja de Pontos/moldura_coracao.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente_conquista`
--

CREATE TABLE `cliente_conquista` (
  `ID_cliente` int(11) NOT NULL,
  `ID_conq` int(11) NOT NULL,
  `data_desbloqueio` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente_item`
--

CREATE TABLE `cliente_item` (
  `ID_cliente` int(11) NOT NULL,
  `ID_item` int(11) NOT NULL,
  `data_compra` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conquista`
--

CREATE TABLE `conquista` (
  `ID_conq` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `pontos` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupom`
--

CREATE TABLE `cupom` (
  `ID_cupom` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo` enum('percentual','valor') NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `validade` date NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `desenvolvedor`
--

CREATE TABLE `desenvolvedor` (
  `ID_desenvolvedor` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `portfolio` varchar(500) DEFAULT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `desenvolvedor`
--

INSERT INTO `desenvolvedor` (`ID_desenvolvedor`, `ID_cliente`, `descricao`, `status`, `portfolio`, `data_inscricao`) VALUES
(2, 2, 'Quero virar fodinha', 0, NULL, '2025-09-28 20:48:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `genero`
--

CREATE TABLE `genero` (
  `Id_Gen` int(11) NOT NULL,
  `Nome` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `genero`
--

INSERT INTO `genero` (`Id_Gen`, `Nome`) VALUES
(1, 'Ação'),
(2, 'Aventura'),
(3, 'RPG'),
(4, 'Estratégia'),
(5, 'Simulação'),
(6, 'Esportes'),
(7, 'Corrida'),
(8, 'Plataforma'),
(9, 'Roguelike'),
(10, 'Visual Novel'),
(11, 'Party Games'),
(12, 'MOBA'),
(13, 'Battle Royale '),
(14, 'Metroidvania'),
(15, 'Shoot\'em up'),
(16, 'Tower Defense'),
(17, 'Tiro (FPS e TPS)'),
(18, 'Puzzle'),
(19, 'Horror'),
(20, 'Sandbox'),
(21, 'Card Games'),
(22, 'Quebra-cabeça Narrativo'),
(23, 'Survival '),
(24, 'Hack and Slash'),
(25, 'Indie');

-- --------------------------------------------------------

--
-- Estrutura para tabela `item_pontos`
--

CREATE TABLE `item_pontos` (
  `ID_item` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `custo_pontos` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo`
--

CREATE TABLE `jogo` (
  `ID_jogo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `desconto` decimal(5,2) DEFAULT 0.00,
  `Img` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `jogo`
--

INSERT INTO `jogo` (`ID_jogo`, `nome`, `preco`, `desconto`, `Img`) VALUES
(1, 'Cuphead', 36.00, NULL, '../../Img/Jogos/cuphead.png'),
(3, 'Hollow Knight', 150.00, NULL, '../../Img/Jogos/hollow_knight.png'),
(4, 'Five Nights at Freddy´s: Into The Pit', 41.00, 30.00, '../../Img/Jogos/into_the_pit.png'),
(5, 'Train SImulator', 90.00, 10.00, '../../Img/Jogos/train.png\r\n'),
(6, 'Surgeon 2', 74.00, 25.00, '../../Img/Jogos/surgeon.png\r\n'),
(7, 'Snow Runner ', 84.00, 14.00, '../../Img/Jogos/snow.png\r\n'),
(8, 'The Sims 4 ', 0.00, 0.00, '../../Img/Jogos/the_sims_4.png\r\n'),
(9, 'Rimworld', 95.00, 5.00, '../../Img/Jogos/rimworld.png\r\n'),
(10, 'Reaper ', 15.00, 10.00, '../../Img/Jogos/reaper.png\r\n'),
(11, 'Project Zomboid ', 55.00, 10.00, '../../Img/Jogos/project_zomboid.png\r\n'),
(12, 'Phasmophobia', 50.00, 5.00, '../../Img/Jogos/phasmophobia.png\r\n'),
(13, 'PC Building ', 50.00, 5.00, '../../Img/Jogos/pc_building.png\r\n'),
(14, 'Oknytt', 15.00, 5.00, '../../Img/Jogos/oknytt.png\r\n'),
(15, 'Limbo ', 30.00, 0.00, '../../Img/Jogos/limbo.png\r\n'),
(16, 'Left 4 Dead', 30.00, 0.00, '../../Img/Jogos/left_4_dead.png\r\n'),
(17, 'House Flipper', 70.00, 15.00, '../../Img/Jogos/house_flipper.png\r\n'),
(18, 'Hollow Knigth: Silksong', 45.00, 0.00, '../../Img/Jogos/hollow_knight_silksong.png\r\n'),
(19, 'GRIS', 40.00, 15.00, '../../Img/Jogos/gris.png\r\n'),
(20, 'Game Dev Tycoon ', 27.00, 0.00, '../../Img/Jogos/game_dev.png\r\n'),
(21, 'Fishing Planet ', 0.00, 0.00, '../../Img/Jogos/fishing_planet.png\r\n'),
(22, 'Euro Truck 2 ', 58.00, 0.00, '../../Img/Jogos/euro_truck.png\r\n'),
(23, 'Don\'t Starve Together ', 25.00, 0.00, '../../Img/Jogos/dont_starve_together.png\r\n'),
(24, 'Dead Cells', 45.00, 0.00, '../../Img/Jogos/dead_cells.png\r\n'),
(25, 'Cooking Simulator', 50.00, 25.00, '../../Img/Jogos/cooking.png\r\n'),
(26, 'Cities Slylines 2', 150.00, 25.00, '../../Img/Jogos/cities_skyline.png\r\n'),
(27, 'Celeste ', 50.00, 15.00, '../../Img/Jogos/celeste.png\r\n'),
(29, 'Bus Simulator 21', 100.00, 15.00, '../../Img/Jogos/bus.png\r\n'),
(30, 'Buckshot Roulette', 8.00, 0.00, '../../Img/Jogos/buckshot_roulette.png\r\n'),
(31, 'Banished ', 30.00, 0.00, '../../Img/Jogos/banished.png\r\n'),
(33, 'Assassin\'s Creed ', 30.00, 0.00, '../../Img/Jogos/AssassinsCreed.jpg'),
(34, 'Unpacking', 15.00, 0.00, '../../Img/Jogos/unpacking.png'),
(35, 'Resident Evil 6', 10.00, 0.00, '../../Img/Jogos/resident_evil.png\r\n'),
(36, 'Need for Speed', 30.00, 0.00, '../../Img/Jogos/need_for_speed.png'),
(37, 'Moonlighter', 35.00, 0.00, '../../Img/Jogos/moonlighter.png'),
(39, 'Have a Nice Death', 20.00, 0.00, '../../Img/Jogos/have_a_nice_death.png'),
(40, 'Goat', 15.00, 0.00, '../../Img/Jogos/goat.png'),
(41, 'Fortnite', 0.00, 0.00, '../../Img/Jogos/Fortnite.jpg'),
(42, 'Cult of the Lamb', 30.00, 0.00, '../../Img/Jogos/cult_of_the_lamb.png'),
(43, 'Bakery Simulator', 45.00, 0.00, '../../Img/Jogos/bakery.png'),
(44, 'Stardew Valey', 30.00, 0.00, '../../Img/Jogos/stardew_valley.png'),
(45, 'The Wild at Heart ', 40.00, 0.00, '../../Img/Jogos/the_wild_at_heart.png'),
(46, 'Skul: The Hero Slayer ', 55.00, 0.00, '../../Img/Jogos/skul.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo_genero`
--

CREATE TABLE `jogo_genero` (
  `ID_jogo` int(11) NOT NULL,
  `Id_Gen` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `jogo_genero`
--

INSERT INTO `jogo_genero` (`ID_jogo`, `Id_Gen`) VALUES
(1, 25),
(2, 22),
(3, 25),
(4, 23),
(5, 5),
(6, 5),
(7, 5),
(8, 5),
(9, 4),
(10, 3),
(11, 5),
(12, 19),
(13, 5),
(14, 5),
(15, 25),
(16, 19),
(17, 5),
(18, 25),
(19, 25),
(20, 5),
(21, 5),
(22, 2),
(23, 25),
(24, 9),
(25, 5),
(26, 4),
(27, 8),
(28, 7),
(29, 7),
(30, 25),
(31, 4),
(32, 17),
(33, 1),
(34, 22),
(35, 19),
(36, 7),
(37, 9),
(38, 3),
(39, 8),
(40, 5),
(41, 13),
(42, 25),
(43, 5),
(44, 3),
(45, 1),
(46, 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_desejo`
--

CREATE TABLE `lista_desejo` (
  `ID_cliente` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lista_desejo`
--

INSERT INTO `lista_desejo` (`ID_cliente`, `ID_jogo`) VALUES
(1, 2),
(2, 16),
(2, 21),
(2, 43),
(3, 11),
(3, 18),
(4, 19);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_item`
--

CREATE TABLE `pedido_item` (
  `ID_item` int(11) NOT NULL,
  `ID_pedido` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_item`
--

INSERT INTO `pedido_item` (`ID_item`, `ID_pedido`, `ID_jogo`, `quantidade`) VALUES
(1, 1, 43, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `resumo_pedido`
--

CREATE TABLE `resumo_pedido` (
  `ID_pedido` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','pago','cancelado') DEFAULT 'pendente',
  `metodo_pagamento` enum('pix','mastercard','paypal') DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `ID_cupom` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `resumo_pedido`
--

INSERT INTO `resumo_pedido` (`ID_pedido`, `ID_cliente`, `data_pedido`, `status`, `metodo_pagamento`, `total`, `ID_cupom`) VALUES
(1, 2, '2025-09-28 16:55:06', 'pendente', 'pix', 45.00, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`ID_avaliacao`);

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`ID_carrinho`),
  ADD UNIQUE KEY `cliente_jogo_unico` (`ID_cliente`,`ID_jogo`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ID_cliente`),
  ADD UNIQUE KEY `CPF` (`CPF`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `cliente_conquista`
--
ALTER TABLE `cliente_conquista`
  ADD PRIMARY KEY (`ID_cliente`,`ID_conq`);

--
-- Índices de tabela `cliente_item`
--
ALTER TABLE `cliente_item`
  ADD PRIMARY KEY (`ID_cliente`,`ID_item`);

--
-- Índices de tabela `conquista`
--
ALTER TABLE `conquista`
  ADD PRIMARY KEY (`ID_conq`);

--
-- Índices de tabela `cupom`
--
ALTER TABLE `cupom`
  ADD PRIMARY KEY (`ID_cupom`);

--
-- Índices de tabela `desenvolvedor`
--
ALTER TABLE `desenvolvedor`
  ADD PRIMARY KEY (`ID_desenvolvedor`);

--
-- Índices de tabela `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`Id_Gen`);

--
-- Índices de tabela `item_pontos`
--
ALTER TABLE `item_pontos`
  ADD PRIMARY KEY (`ID_item`);

--
-- Índices de tabela `jogo`
--
ALTER TABLE `jogo`
  ADD PRIMARY KEY (`ID_jogo`);

--
-- Índices de tabela `lista_desejo`
--
ALTER TABLE `lista_desejo`
  ADD PRIMARY KEY (`ID_cliente`,`ID_jogo`);

--
-- Índices de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD PRIMARY KEY (`ID_item`);

--
-- Índices de tabela `resumo_pedido`
--
ALTER TABLE `resumo_pedido`
  ADD PRIMARY KEY (`ID_pedido`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `ID_avaliacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `ID_carrinho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `ID_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `conquista`
--
ALTER TABLE `conquista`
  MODIFY `ID_conq` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cupom`
--
ALTER TABLE `cupom`
  MODIFY `ID_cupom` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `desenvolvedor`
--
ALTER TABLE `desenvolvedor`
  MODIFY `ID_desenvolvedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `genero`
--
ALTER TABLE `genero`
  MODIFY `Id_Gen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `item_pontos`
--
ALTER TABLE `item_pontos`
  MODIFY `ID_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `jogo`
--
ALTER TABLE `jogo`
  MODIFY `ID_jogo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  MODIFY `ID_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `resumo_pedido`
--
ALTER TABLE `resumo_pedido`
  MODIFY `ID_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

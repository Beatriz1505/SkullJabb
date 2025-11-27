-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27-Nov-2025 às 17:22
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `skulljabb`
--
CREATE DATABASE IF NOT EXISTS `skulljabb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skulljabb`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `amizade`
--

CREATE TABLE `amizade` (
  `id_amizade` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `id_recebedor` int(11) NOT NULL,
  `status` enum('pendente','aceito','recusado') DEFAULT 'pendente',
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_resposta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `amizade`
--

INSERT INTO `amizade` (`id_amizade`, `id_solicitante`, `id_recebedor`, `status`, `data_solicitacao`, `data_resposta`) VALUES
(6, 1, 3, 'aceito', '2025-10-28 19:30:12', '2025-10-28 23:19:47'),
(10, 5, 3, 'recusado', '2025-10-29 02:26:36', '2025-10-29 01:04:08'),
(11, 5, 1, 'pendente', '2025-10-29 02:26:40', NULL),
(13, 7, 3, 'aceito', '2025-10-29 02:54:16', '2025-10-29 00:19:39'),
(14, 8, 3, 'aceito', '2025-10-29 02:56:04', '2025-10-29 00:19:44'),
(16, 11, 3, 'recusado', '2025-10-29 03:17:36', '2025-10-29 01:09:56'),
(18, 11, 3, 'aceito', '2025-11-02 01:28:33', '2025-11-01 22:35:43'),
(20, 9, 3, 'aceito', '2025-11-02 01:36:20', '2025-11-01 22:42:01'),
(22, 1, 7, 'aceito', '2025-11-02 01:49:34', '2025-11-27 13:04:14'),
(23, 1, 8, 'pendente', '2025-11-02 01:49:38', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacao`
--

CREATE TABLE `avaliacao` (
  `ID_avaliacao` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `nota` tinyint(4) NOT NULL,
  `comentario` text DEFAULT NULL,
  `data_avaliacao` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `avaliacao`
--

INSERT INTO `avaliacao` (`ID_avaliacao`, `ID_cliente`, `ID_jogo`, `nota`, `comentario`, `data_avaliacao`) VALUES
(10, 4, 19, 4, 'Interessante', '2025-09-28 23:01:49'),
(11, 3, 11, 4, 'Esse jogo é muito bem desenvolvido!', '2025-10-29 01:03:42'),
(12, 3, 17, 3, 'Jogo muito interessante', '2025-10-29 01:07:03'),
(13, 3, 44, 5, 'Jogo Lindo!', '2025-10-29 01:08:11'),
(14, 3, 3, 5, 'Jogo incrível!', '2025-10-29 01:09:14'),
(15, 3, 3, 3, 'Adoro esse jogo', '2025-10-29 01:20:56');

-- --------------------------------------------------------

--
-- Estrutura da tabela `biblioteca`
--

CREATE TABLE `biblioteca` (
  `id_biblioteca` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_jogo` int(11) NOT NULL,
  `data_compra` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `biblioteca`
--

INSERT INTO `biblioteca` (`id_biblioteca`, `id_cliente`, `id_jogo`, `data_compra`) VALUES
(1, 3, 18, '2025-10-29 00:24:03'),
(2, 3, 3, '2025-10-29 00:44:55'),
(3, 1, 15, '2025-10-29 00:57:36'),
(4, 1, 18, '2025-10-29 00:57:44'),
(5, 1, 23, '2025-10-29 00:57:50');

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `ID_carrinho` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `carrinho`
--

INSERT INTO `carrinho` (`ID_carrinho`, `ID_cliente`, `ID_jogo`, `quantidade`) VALUES
(19, 2, 44, 1),
(24, 6, 4, 1),
(25, 6, 12, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente`
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
  `moldura` varchar(255) NOT NULL,
  `ultimo_resgate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `cliente`
--

INSERT INTO `cliente` (`ID_cliente`, `email`, `CPF`, `nome`, `senha`, `pontos`, `usuario`, `foto`, `moldura`, `ultimo_resgate`) VALUES
(1, 'galdinob596@gmail.com', '501.988.228-85', 'Bea', '$2y$10$FfLDzgzMPTt10nkYi6VWOeXevDDlwdeVJ6IaoeiFpSZ70FpA1b63O', 50, 'Bellatrizx', '../../Img/Perfis/castiel supernatural icon.jpeg', '../../Img/Loja de Pontos/moldura_natalina.png', '2025-11-23'),
(3, 'EstherGaldino@gmail.com', '212.252.365-78', 'Esther', '$2y$10$s4yp9GD50NlcBIi24y26IOFfclbqMyuPTG9oEyiRS3oEnD.SfIrb6', 130, 'Ste', '../../Img/Perfis/download (4).jpeg', '../../Img/Loja de Pontos/moldura_cogumelo.png', '2025-11-01'),
(5, 'bellatrizxgaldinotorres@gmail.com', '501.288.955-86', 'biabia', '$2y$10$iI6OsNkHUR3FAw887IIb5uCPhnf6w9LWnUa9URr2PPB8Cu8G67Rje', 50, 'bellatrizx', '../../Img/Perfis/Snoopy Reading A Book _) Sticker.jpeg', '', NULL),
(6, 'Jeeh_iero@gmail.com', '222.222.222-22', 'deta', '$2y$10$lgYbylQmo4s/YHKFRgne/ulSPUnjjtCm0BhWx3WjFTblGG/t6skcO', 50, 'Heyhugme', '../../Img/Perfis/Arthur.jpeg', '', NULL),
(7, 'GabrielleSouza@gmail.com', '111.111.111-11', 'Aby', '$2y$10$M0NAMrSYgpUtu7cyg57IHOlsb7T9sUT0wrgdxYPzhW1g53xV9IW1y', 50, 'Bagi', '../../Img/Perfis/Sam.jpeg', '../../Img/Loja de Pontos/moldura_cogumelo.png', NULL),
(8, 'YasmimBorges@gmail.com', '222.222.222-20', 'Mamim', '$2y$10$JO95a1cDnM/HOQa4PYroOeKUz2mbym/L4RZgInb4qM7Qz1Gnf9pLO', 50, 'Yasbomel', '../../Img/Perfis/Dean Winchester icon.jpeg', '../../Img/Loja de Pontos/moldura_caveira.png', NULL),
(9, 'NicollasLopes@gmail.com', '777.777.777-77', 'Nicollas', '$2y$10$pfs1dGLx8Mtpd9WZhd9q3eI27DYiyt1FGmU5qYp3RZMgFBeJx85JG', 50, 'nicollaszxn', '../../Img/Perfis/WhatsApp Image 2025-10-29 at 12.09.19 AM.jpeg', '../../Img/Loja de Pontos/moldura_coracao.png', NULL),
(10, 'AnaLivia@gmail.com', '444.444.444-44', 'Nali', '$2y$10$RQqiglvuKToNwIVPhm1Jd.wfn0MRov2ezpCxF.YV00OQ5aEfqgJMa', 50, 'Analivia', '../../Img/Perfis/Andressa Urach usa microshort ao posar com top da Seleção.jpeg', '', NULL),
(11, 'JayaneElias@gmail.com', '444.444.444-40', 'Jay', '$2y$10$hjwYyi7Rmoho3l7A6uuAJu2dqE8lGfw8GsqForUCwgXjkV4qCGbjS', 50, 'Jayane', '../../Img/Perfis/890fa97d-4547-4370-84dd-02f359d210d6.jpeg', '', NULL),
(12, 'BeatrizBatista@gmail.com', '555.555.555-50', 'Bia', '$2y$10$skeH08/BGcaf32WCBHBqUOxitlzZBkLydYiDsiFpw3.pbJCY/fJl2', 50, 'Biaxx', '../../Img/Perfis/༻𓊈𒆜hello kitty 𒆜𓊉༺.jpeg', '', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente_conquista`
--

CREATE TABLE `cliente_conquista` (
  `ID_cliente` int(11) NOT NULL,
  `ID_conq` int(11) NOT NULL,
  `data_desbloqueio` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente_item`
--

CREATE TABLE `cliente_item` (
  `ID_cliente` int(11) NOT NULL,
  `ID_item` int(11) NOT NULL,
  `data_compra` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente_jogo`
--

CREATE TABLE `cliente_jogo` (
  `id_cliente_jogo` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_jogo` int(11) NOT NULL,
  `horas_jogadas` decimal(6,1) DEFAULT 0.0,
  `data_ultimo_jogo` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `conquista`
--

CREATE TABLE `conquista` (
  `ID_conq` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `pontos` int(11) DEFAULT 0,
  `img` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `conquista`
--

INSERT INTO `conquista` (`ID_conq`, `ID_jogo`, `nome`, `descricao`, `pontos`, `img`) VALUES
(1, 15, 'Altitude é Atitude', 'Exploração no ar', 10, '../../Img/Conquistas/Limbo/c1.png'),
(2, 15, 'Direção Errada', 'Isso não está certo', 10, '../../Img/Conquistas/Limbo/c2.png'),
(3, 15, 'O Crédito a Quem é Devido', 'A persistência é a sua própria recompensa', 20, '../../Img/Conquistas/Limbo/c9.png'),
(4, 15, 'Sozinho no Escuro', 'Sob o artrópode', 10, '../../Img/Conquistas/Limbo/c11.jpg'),
(5, 15, 'Guiado pelas Faíscas', 'O segredo está no caixote', 15, '../../Img/Conquistas/Limbo/c6.png'),
(6, 15, 'Voltar Atrás', 'Sobe os caixotes', 10, '../../Img/Conquistas/Limbo/c5.png'),
(7, 15, 'Exploração Urbana', 'Envolve levantar coisas pesadas', 15, '../../Img/Conquistas/Limbo/c12.png'),
(8, 15, 'Subsolo', 'Passagem vertical', 20, '../../Img/Conquistas/Limbo/c7.png'),
(9, 15, 'A Subir', 'Não fiques em baixo por causa da gravidade', 20, '../../Img/Conquistas/Limbo/c8.png'),
(10, 15, 'Está Preso', 'Prepara um desembarque', 15, '../../Img/Conquistas/Limbo/c3.png'),
(11, 15, 'Escalar a Engrenagem', 'Não puxes a alavanca só porque podes', 25, '../../Img/Conquistas/Limbo/c4.png'),
(12, 15, 'DING', NULL, 30, '../../Img/Conquistas/Limbo/c10.png'),
(13, 15, 'Não Vale a Pena Morrer', 'Acaba o jogo de uma assentada com cinco mortes ou menos', 30, '../../Img/Conquistas/Limbo/c13.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cupom`
--

CREATE TABLE `cupom` (
  `ID_cupom` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo` enum('percentual','valor') NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `validade` date NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `desenvolvedor`
--

CREATE TABLE `desenvolvedor` (
  `ID_desenvolvedor` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `portfolio` varchar(500) DEFAULT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `desenvolvedor`
--

INSERT INTO `desenvolvedor` (`ID_desenvolvedor`, `ID_cliente`, `descricao`, `status`, `portfolio`, `data_inscricao`) VALUES
(2, 2, 'Quero virar fodinha', 0, NULL, '2025-09-28 20:48:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `genero`
--

CREATE TABLE `genero` (
  `Id_Gen` int(11) NOT NULL,
  `Nome` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `genero`
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
-- Estrutura da tabela `inventario_pontos`
--

CREATE TABLE `inventario_pontos` (
  `id_inventario` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `data_compra` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `inventario_pontos`
--

INSERT INTO `inventario_pontos` (`id_inventario`, `id_cliente`, `id_item`, `data_compra`) VALUES
(3, 1, 2, '2025-11-23 12:56:35');

-- --------------------------------------------------------

--
-- Estrutura da tabela `item_pontos`
--

CREATE TABLE `item_pontos` (
  `ID_item` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `custo_pontos` int(11) NOT NULL,
  `Img` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `item_pontos`
--

INSERT INTO `item_pontos` (`ID_item`, `nome`, `descricao`, `tipo`, `custo_pontos`, `Img`) VALUES
(1, 'Gris', 'Jogo Gris', 'Jogo', 1550, '../../Img/Jogos/gris.png'),
(2, 'Moldura Natal', 'Moldura com temas natalinos', 'Moldura', 250, '../../Img/Loja de Pontos/moldura_natalina.png'),
(3, 'Moldura Natureza', 'Moldura com cogumelos, flores e outros elementos', 'Moldura', 250, '../../Img/Loja de Pontos/moldura_natureza_1.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogo`
--

CREATE TABLE `jogo` (
  `ID_jogo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `desconto` decimal(5,2) DEFAULT 0.00,
  `Img` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `jogo`
--

INSERT INTO `jogo` (`ID_jogo`, `nome`, `preco`, `desconto`, `Img`, `banner`) VALUES
(1, 'Cuphead', '36.00', NULL, '../../Img/Jogos/cuphead.png', NULL),
(3, 'Hollow Knight', '150.00', NULL, '../../Img/Jogos/hollow_knight.png', NULL),
(4, 'Five Nights at Freddy´s: Into The Pit', '41.00', '30.00', '../../Img/Jogos/into_the_pit.png', NULL),
(5, 'Train SImulator', '90.00', '10.00', '../../Img/Jogos/train.png\r\n', NULL),
(6, 'Surgeon 2', '74.00', '25.00', '../../Img/Jogos/surgeon.png\r\n', NULL),
(7, 'Snow Runner ', '84.00', '14.00', '../../Img/Jogos/snow.png\r\n', NULL),
(8, 'The Sims 4 ', '0.00', '0.00', '../../Img/Jogos/the_sims_4.png\r\n', NULL),
(9, 'Rimworld', '95.00', '5.00', '../../Img/Jogos/rimworld.png\r\n', NULL),
(10, 'Reaper ', '15.00', '10.00', '../../Img/Jogos/reaper.png\r\n', NULL),
(11, 'Project Zomboid ', '55.00', '10.00', '../../Img/Jogos/project_zomboid.png\r\n', '../../Img/Conquistas/Banners/project_zomboid.jpg'),
(12, 'Phasmophobia', '50.00', '5.00', '../../Img/Jogos/phasmophobia.png\r\n', NULL),
(13, 'PC Building ', '50.00', '5.00', '../../Img/Jogos/pc_building.png\r\n', NULL),
(14, 'Oknytt', '15.00', '5.00', '../../Img/Jogos/oknytt.png\r\n', NULL),
(15, 'Limbo ', '30.00', '0.00', '../../Img/Jogos/limbo.png\r\n', '../../Img/Conquistas/Banners/limbo.png'),
(16, 'Left 4 Dead', '30.00', '0.00', '../../Img/Jogos/left_4_dead.png\r\n', NULL),
(17, 'House Flipper', '70.00', '15.00', '../../Img/Jogos/house_flipper.png\r\n', NULL),
(18, 'Hollow Knigth: Silksong', '45.00', '0.00', '../../Img/Jogos/hollow_knight_silksong.png\r\n', NULL),
(19, 'GRIS', '40.00', '15.00', '../../Img/Jogos/gris.png\r\n', NULL),
(20, 'Game Dev Tycoon ', '27.00', '0.00', '../../Img/Jogos/game_dev.png\r\n', NULL),
(21, 'Fishing Planet ', '0.00', '0.00', '../../Img/Jogos/fishing_planet.png\r\n', NULL),
(22, 'Euro Truck 2 ', '58.00', '0.00', '../../Img/Jogos/euro_truck.png\r\n', NULL),
(23, 'Don\'t Starve Together ', '25.00', '0.00', '../../Img/Jogos/dont_starve_together.png\r\n', NULL),
(24, 'Dead Cells', '45.00', '0.00', '../../Img/Jogos/dead_cells.png\r\n', NULL),
(25, 'Cooking Simulator', '50.00', '25.00', '../../Img/Jogos/cooking.png\r\n', NULL),
(26, 'Cities Slylines 2', '150.00', '25.00', '../../Img/Jogos/cities_skyline.png\r\n', NULL),
(27, 'Celeste ', '50.00', '15.00', '../../Img/Jogos/celeste.png\r\n', NULL),
(29, 'Bus Simulator 21', '100.00', '15.00', '../../Img/Jogos/bus.png\r\n', NULL),
(30, 'Buckshot Roulette', '8.00', '0.00', '../../Img/Jogos/buckshot_roulette.png\r\n', NULL),
(31, 'Banished ', '30.00', '0.00', '../../Img/Jogos/banished.png\r\n', NULL),
(33, 'Assassin\'s Creed ', '30.00', '0.00', '../../Img/Jogos/AssassinsCreed.jpg', NULL),
(34, 'Unpacking', '15.00', '0.00', '../../Img/Jogos/unpacking.png', NULL),
(35, 'Resident Evil 6', '10.00', '0.00', '../../Img/Jogos/resident_evil.png\r\n', NULL),
(36, 'Need for Speed', '30.00', '0.00', '../../Img/Jogos/need_for_speed.png', NULL),
(37, 'Moonlighter', '35.00', '0.00', '../../Img/Jogos/moonlighter.png', NULL),
(39, 'Have a Nice Death', '20.00', '0.00', '../../Img/Jogos/have_a_nice_death.png', NULL),
(40, 'Goat', '15.00', '0.00', '../../Img/Jogos/goat.png', NULL),
(41, 'Fortnite', '0.00', '0.00', '../../Img/Jogos/Fortnite.jpg', NULL),
(42, 'Cult of the Lamb', '30.00', '0.00', '../../Img/Jogos/cult_of_the_lamb.png', NULL),
(43, 'Bakery Simulator', '45.00', '0.00', '../../Img/Jogos/bakery.png', NULL),
(44, 'Stardew Valey', '30.00', '0.00', '../../Img/Jogos/stardew_valley.png', NULL),
(45, 'The Wild at Heart ', '40.00', '0.00', '../../Img/Jogos/the_wild_at_heart.png', NULL),
(46, 'Skul: The Hero Slayer ', '55.00', '0.00', '../../Img/Jogos/skul.png', NULL),
(47, 'Omori', '37.00', '10.00', '../../Img/Jogos/omori.png', '../../Img/Conquistas/Banners/omori.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogo_genero`
--

CREATE TABLE `jogo_genero` (
  `ID_jogo` int(11) NOT NULL,
  `Id_Gen` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `jogo_genero`
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
(46, 8),
(47, 25);

-- --------------------------------------------------------

--
-- Estrutura da tabela `lista_desejo`
--

CREATE TABLE `lista_desejo` (
  `ID_cliente` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `lista_desejo`
--

INSERT INTO `lista_desejo` (`ID_cliente`, `ID_jogo`) VALUES
(1, 2),
(2, 16),
(2, 21),
(2, 43),
(3, 44),
(3, 46),
(4, 19),
(6, 1),
(6, 11),
(6, 18),
(6, 34);

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao`
--

CREATE TABLE `notificacao` (
  `id_notificacao` int(11) NOT NULL,
  `id_destinatario` int(11) NOT NULL,
  `id_remetente` int(11) DEFAULT NULL,
  `tipo` enum('amizade','sistema','mensagem','outro') DEFAULT 'amizade',
  `mensagem` text DEFAULT NULL,
  `lido` tinyint(1) DEFAULT 0,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_item`
--

CREATE TABLE `pedido_item` (
  `ID_item` int(11) NOT NULL,
  `ID_pedido` int(11) NOT NULL,
  `ID_jogo` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `pedido_item`
--

INSERT INTO `pedido_item` (`ID_item`, `ID_pedido`, `ID_jogo`, `quantidade`) VALUES
(1, 1, 43, 1),
(2, 2, 34, 1),
(3, 2, 46, 1),
(4, 3, 34, 1),
(5, 4, 21, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `resumo_pedido`
--

CREATE TABLE `resumo_pedido` (
  `ID_pedido` int(11) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','pago','cancelado') DEFAULT 'pendente',
  `metodo_pagamento` enum('pix','mastercard','paypal') DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `ID_cupom` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `resumo_pedido`
--

INSERT INTO `resumo_pedido` (`ID_pedido`, `ID_cliente`, `data_pedido`, `status`, `metodo_pagamento`, `total`, `ID_cupom`) VALUES
(1, 2, '2025-09-28 16:55:06', 'pendente', 'pix', '45.00', NULL),
(2, 3, '2025-11-01 22:32:47', 'pendente', 'paypal', '70.00', NULL),
(3, 3, '2025-11-01 22:42:35', 'pendente', 'paypal', '15.00', NULL),
(4, 1, '2025-11-23 11:53:22', 'pendente', 'pix', '0.00', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `suporte`
--

CREATE TABLE `suporte` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `amizade`
--
ALTER TABLE `amizade`
  ADD PRIMARY KEY (`id_amizade`),
  ADD KEY `id_solicitante` (`id_solicitante`),
  ADD KEY `id_recebedor` (`id_recebedor`);

--
-- Índices para tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`ID_avaliacao`);

--
-- Índices para tabela `biblioteca`
--
ALTER TABLE `biblioteca`
  ADD PRIMARY KEY (`id_biblioteca`),
  ADD KEY `idx_bib_cliente` (`id_cliente`),
  ADD KEY `idx_bib_jogo` (`id_jogo`);

--
-- Índices para tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`ID_carrinho`),
  ADD UNIQUE KEY `cliente_jogo_unico` (`ID_cliente`,`ID_jogo`);

--
-- Índices para tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ID_cliente`),
  ADD UNIQUE KEY `CPF` (`CPF`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `cliente_conquista`
--
ALTER TABLE `cliente_conquista`
  ADD PRIMARY KEY (`ID_cliente`,`ID_conq`);

--
-- Índices para tabela `cliente_item`
--
ALTER TABLE `cliente_item`
  ADD PRIMARY KEY (`ID_cliente`,`ID_item`);

--
-- Índices para tabela `cliente_jogo`
--
ALTER TABLE `cliente_jogo`
  ADD PRIMARY KEY (`id_cliente_jogo`),
  ADD KEY `idx_cliente` (`id_cliente`),
  ADD KEY `idx_jogo` (`id_jogo`);

--
-- Índices para tabela `conquista`
--
ALTER TABLE `conquista`
  ADD PRIMARY KEY (`ID_conq`);

--
-- Índices para tabela `cupom`
--
ALTER TABLE `cupom`
  ADD PRIMARY KEY (`ID_cupom`);

--
-- Índices para tabela `desenvolvedor`
--
ALTER TABLE `desenvolvedor`
  ADD PRIMARY KEY (`ID_desenvolvedor`);

--
-- Índices para tabela `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`Id_Gen`);

--
-- Índices para tabela `inventario_pontos`
--
ALTER TABLE `inventario_pontos`
  ADD PRIMARY KEY (`id_inventario`),
  ADD UNIQUE KEY `unique_cliente_item` (`id_cliente`,`id_item`);

--
-- Índices para tabela `item_pontos`
--
ALTER TABLE `item_pontos`
  ADD PRIMARY KEY (`ID_item`);

--
-- Índices para tabela `jogo`
--
ALTER TABLE `jogo`
  ADD PRIMARY KEY (`ID_jogo`);

--
-- Índices para tabela `lista_desejo`
--
ALTER TABLE `lista_desejo`
  ADD PRIMARY KEY (`ID_cliente`,`ID_jogo`);

--
-- Índices para tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD PRIMARY KEY (`id_notificacao`),
  ADD KEY `idx_destinatario` (`id_destinatario`),
  ADD KEY `idx_remetente` (`id_remetente`);

--
-- Índices para tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD PRIMARY KEY (`ID_item`);

--
-- Índices para tabela `resumo_pedido`
--
ALTER TABLE `resumo_pedido`
  ADD PRIMARY KEY (`ID_pedido`);

--
-- Índices para tabela `suporte`
--
ALTER TABLE `suporte`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `amizade`
--
ALTER TABLE `amizade`
  MODIFY `id_amizade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `ID_avaliacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `biblioteca`
--
ALTER TABLE `biblioteca`
  MODIFY `id_biblioteca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `ID_carrinho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `ID_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `cliente_jogo`
--
ALTER TABLE `cliente_jogo`
  MODIFY `id_cliente_jogo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conquista`
--
ALTER TABLE `conquista`
  MODIFY `ID_conq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- AUTO_INCREMENT de tabela `inventario_pontos`
--
ALTER TABLE `inventario_pontos`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `item_pontos`
--
ALTER TABLE `item_pontos`
  MODIFY `ID_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `jogo`
--
ALTER TABLE `jogo`
  MODIFY `ID_jogo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de tabela `notificacao`
--
ALTER TABLE `notificacao`
  MODIFY `id_notificacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  MODIFY `ID_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `resumo_pedido`
--
ALTER TABLE `resumo_pedido`
  MODIFY `ID_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `suporte`
--
ALTER TABLE `suporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `amizade`
--
ALTER TABLE `amizade`
  ADD CONSTRAINT `amizade_ibfk_1` FOREIGN KEY (`id_solicitante`) REFERENCES `cliente` (`ID_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `amizade_ibfk_2` FOREIGN KEY (`id_recebedor`) REFERENCES `cliente` (`ID_cliente`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

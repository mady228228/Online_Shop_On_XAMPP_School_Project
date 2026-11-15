-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 15 2026 г., 02:33
-- Версия сервера: 10.4.27-MariaDB
-- Версия PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `comfort_shop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE `clients` (
  `clientID` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone_number` varchar(19) NOT NULL,
  `home_address` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Дамп данных таблицы `clients`
--

INSERT INTO `clients` (`clientID`, `nickname`, `password_hash`, `phone_number`, `home_address`, `created_at`) VALUES
(1, 'demo', 'demo123', '+7 (777) 222 22-22', 'Pushkin st. 52', '2026-01-28 10:14:44'),
(2, 'minecrafter123', '$2y$10$3Cjp73H/XoikEzXneaTdTuTHztR00/EM8EHfMtcFY0nnisKZjN9ii', '+7 700 300 9710', 'voroshilova 52', '2026-01-28 10:27:36'),
(3, 'hfgisgfdoisofdi', 'Mady2288', '+76666666666', 'skebobob', '2026-04-03 13:11:59'),
(4, 'gsfgargdfg', '12345678', '+77070707070', 'voroshilova 52', '2026-10-13 12:21:41'),
(5, 'wqeeqrwer', '123456', '+7 700 300 9710', 'redkii 34', '2026-10-13 12:23:50'),
(6, 'mady228', '123456', '+77007007070', 'gigabut', '2026-11-13 04:10:31');

-- --------------------------------------------------------

--
-- Структура таблицы `goods`
--

CREATE TABLE `goods` (
  `goodID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Дамп данных таблицы `goods`
--

INSERT INTO `goods` (`goodID`, `name`, `description`, `cost`, `type`, `count`, `image_url`, `created_at`) VALUES
(1, 'Decor Vase', 'Minimalistic vase for home decor.', 7990, 'home decor', 0, NULL, '2026-01-28 10:14:44'),
(2, 'Chair', 'Comfortable chair for kitchen or office.', 22000, 'furniture', 3, NULL, '2026-01-28 10:14:44'),
(3, 'Wall Clock', 'Silent wall clock, modern style.', 8990, 'home decor', 2, NULL, '2026-01-28 10:14:44'),
(4, 'plate', '', 2990, 'dish', 22, NULL, '2026-10-13 14:33:38'),
(5, 'keyboards', 'keuboard for personal computer', 30000, 'pc device', 67, NULL, '2026-11-13 04:13:59');

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `messageID` int(11) NOT NULL,
  `clientID` int(11) NOT NULL,
  `workerID` int(11) NOT NULL,
  `sender_role` enum('client','worker') NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`messageID`, `clientID`, `workerID`, `sender_role`, `body`, `created_at`) VALUES
(3, 3, 2, 'client', 'dshgsdgfbds', '2026-04-03 14:14:43'),
(4, 3, 2, 'client', 'sdafasdf', '2026-04-03 14:32:33'),
(5, 3, 2, 'worker', 'adfsadfa', '2026-04-03 14:32:41'),
(6, 3, 2, 'worker', 'fargfsdfg', '2026-04-08 10:23:19'),
(7, 1, 2, 'client', 'rergsdfg', '2026-04-08 10:23:39'),
(8, 1, 2, 'client', 'salam popolam', '2026-10-13 14:31:47'),
(9, 6, 2, 'client', 'hdadkglsd', '2026-11-13 04:11:18'),
(10, 1, 2, 'worker', 'uagaleykum', '2026-11-13 04:14:57');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `clientID` int(11) NOT NULL,
  `workerID` int(11) NOT NULL,
  `goodID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `date_of_order` datetime NOT NULL DEFAULT current_timestamp(),
  `cost_of_order` float NOT NULL,
  `status` enum('new','processing','completed','cancelled') NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`orderID`, `clientID`, `workerID`, `goodID`, `quantity`, `date_of_order`, `cost_of_order`, `status`) VALUES
(2, 1, 2, 1, 5, '2026-04-08 10:22:25', 29950, 'completed'),
(3, 1, 2, 3, 1, '2026-04-08 10:22:25', 8990, 'completed'),
(4, 1, 2, 2, 3, '2026-04-13 08:41:14', 59970, 'completed'),
(5, 1, 2, 3, 9, '2026-04-13 08:41:14', 80910, 'cancelled'),
(6, 6, 3, 4, 3, '2026-11-13 04:11:33', 8970, 'new');

-- --------------------------------------------------------

--
-- Структура таблицы `workers`
--

CREATE TABLE `workers` (
  `workerID` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone_number` varchar(19) NOT NULL,
  `salary` float NOT NULL DEFAULT 0,
  `bonus` float NOT NULL DEFAULT 0,
  `owner_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Дамп данных таблицы `workers`
--

INSERT INTO `workers` (`workerID`, `fullname`, `password_hash`, `phone_number`, `salary`, `bonus`, `owner_status`, `created_at`) VALUES
(1, 'owner', 'owner123', '+7 (777) 000 00-00', 0, 0, 1, '2026-01-28 10:14:44'),
(2, 'worker', 'worker123', '+7 (777) 111 11-11', 200000, 1978.2, 0, '2026-01-28 10:14:44'),
(3, 'kill', 'qwerty2288', '+77070707070', 34500, 0, 0, '2026-04-03 13:25:20');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`clientID`),
  ADD UNIQUE KEY `nickname` (`nickname`);

--
-- Индексы таблицы `goods`
--
ALTER TABLE `goods`
  ADD PRIMARY KEY (`goodID`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageID`),
  ADD KEY `fk_msg_client` (`clientID`),
  ADD KEY `fk_msg_worker` (`workerID`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `fk_orders_client` (`clientID`),
  ADD KEY `fk_orders_worker` (`workerID`),
  ADD KEY `fk_orders_good` (`goodID`);

--
-- Индексы таблицы `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`workerID`),
  ADD UNIQUE KEY `fullname` (`fullname`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `clientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `goods`
--
ALTER TABLE `goods`
  MODIFY `goodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `messageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `workers`
--
ALTER TABLE `workers`
  MODIFY `workerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_client` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`),
  ADD CONSTRAINT `fk_msg_worker` FOREIGN KEY (`workerID`) REFERENCES `workers` (`workerID`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_client` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`),
  ADD CONSTRAINT `fk_orders_good` FOREIGN KEY (`goodID`) REFERENCES `goods` (`goodID`),
  ADD CONSTRAINT `fk_orders_worker` FOREIGN KEY (`workerID`) REFERENCES `workers` (`workerID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

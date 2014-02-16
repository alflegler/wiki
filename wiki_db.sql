-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 16 2014 г., 19:42
-- Версия сервера: 5.6.12-log
-- Версия PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `wiki_db`
--
CREATE DATABASE IF NOT EXISTS `wiki_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `wiki_db`;

-- --------------------------------------------------------

--
-- Структура таблицы `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `parentPath` varchar(255) DEFAULT NULL,
  `pageName` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pagePathIndex` (`parentPath`,`pageName`),
  KEY `FI_ent` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Дамп данных таблицы `page`
--

INSERT INTO `page` (`id`, `parent_id`, `parentPath`, `pageName`, `title`, `text`) VALUES
(10, NULL, NULL, '', 'Welcome!', 'welcome to **wiki**!'),
(11, 10, '', 'page2', 'tit1', '[[http://yandex.ru Яндекс]] — ссылка на внешний ресурс. Текст ссылки —  <Яндекс>.\r\n<h1>должен быть обычный текст</h1>\r\nКавычки ASCII в тексте заменяются на "ёлочки".'),
(14, 10, '', 'page7123123', 'qwe', 'qwe'),
(17, 10, '', 'hello', 'hello', 'Hello, World!\r\n - Hi');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `parent` FOREIGN KEY (`parent_id`) REFERENCES `page` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

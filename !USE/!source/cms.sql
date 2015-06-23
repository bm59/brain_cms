-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 08 2012 г., 12:04
-- Версия сервера: 5.5.11
-- Версия PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `cms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `site_bk_users_info`
--

CREATE TABLE IF NOT EXISTS `site_bk_users_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `regdate` date DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `pswd` varchar(255) DEFAULT NULL,
  `type` bigint(20) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `secondname` varchar(255) DEFAULT NULL,
  `parentname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `picture` bigint(20) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `site_bk_users_info`
--

INSERT INTO `site_bk_users_info` (`id`, `regdate`, `login`, `pswd`, `type`, `firstname`, `secondname`, `parentname`, `email`, `picture`, `settings`) VALUES
(1, '2011-05-25', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 'admin', 'admin', 'admin', '', 0, '|engage|notypechange|norename|nologinchange|undeletable|noswitch|help=users_mainadmin|lasttime=1328691581|');

-- --------------------------------------------------------

--
-- Структура таблицы `site_bk_users_types`
--

CREATE TABLE IF NOT EXISTS `site_bk_users_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `access` text,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `site_bk_users_types`
--

INSERT INTO `site_bk_users_types` (`id`, `name`, `access`, `settings`) VALUES
(1, 'Администраторы', '', '|undeletable|superaccess|noedit|help=usergroups_admins|');

-- --------------------------------------------------------

--
-- Структура таблицы `site_logs`
--

CREATE TABLE IF NOT EXISTS `site_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `sectionname` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `section_id` varchar(255) DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `site_logs`
--


-- --------------------------------------------------------

--
-- Структура таблицы `site_settings`
--

CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value` text,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `site_settings`
--


-- --------------------------------------------------------

--
-- Структура таблицы `site_site_data_sets`
--

CREATE TABLE IF NOT EXISTS `site_site_data_sets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `site_site_data_sets`
--

INSERT INTO `site_site_data_sets` (`id`, `description`, `name`, `settings`) VALUES
(1, 'Лист (1 колонка)', 'sheet1', ''),
(2, 'Справочник', 'spr', ''),
(3, 'Публикация', 'publication', '');

-- --------------------------------------------------------

--
-- Структура таблицы `site_site_data_types`
--

CREATE TABLE IF NOT EXISTS `site_site_data_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dataset` bigint(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `precedence` bigint(20) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `site_site_data_types`
--

INSERT INTO `site_site_data_types` (`id`, `dataset`, `description`, `name`, `type`, `precedence`, `settings`) VALUES
(1, 1, 'Текст', 'text', 'CDTextEditor', 0, '|important|texttype=full|'),
(2, 2, 'Заголовок', 'header', 'CDText', 0, '|important|'),
(3, 2, 'Описание', 'description', 'CDText', 1, ''),
(4, 3, 'Дата', 'date', 'CDDate', 0, ''),
(5, 3, 'Заголовок', 'header', 'CDText', 1, '|important|'),
(6, 3, 'Анонс', 'short', 'CDText', 2, '|important|'),
(7, 3, 'Текст', 'text', 'CDTextEditor', 3, '|important|texttype=full|'),
(8, 3, 'Теги, разделитель -"|"', 'tags', 'CDText', 4, '');

-- --------------------------------------------------------

--
-- Структура таблицы `site_site_sections`
--

CREATE TABLE IF NOT EXISTS `site_site_sections` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `pattern` varchar(255) DEFAULT NULL,
  `parent` bigint(20) DEFAULT NULL,
  `precedence` bigint(20) DEFAULT NULL,
  `isservice` int(1) DEFAULT NULL,
  `keywords` text,
  `title` text,
  `tags` text,
  `description` text,
  `visible` tinyint(6) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `site_site_sections`
--

INSERT INTO `site_site_sections` (`id`, `name`, `path`, `pattern`, `parent`, `precedence`, `isservice`, `keywords`, `title`, `tags`, `description`, `visible`, `settings`) VALUES
(1, 'Управление сайтом', 'control', 'PFolder', 0, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(2, 'Настройки', 'settings', 'PFolder', 1, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(3, 'Доступ', 'access', 'PFolder', 0, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(4, 'Пользователи', 'users', 'PFolder', 3, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(5, 'Группы', 'groups', 'PFolder', 3, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(6, 'Содержимое сайта', 'sitecontent', 'PFolder', 0, 0, 0, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|');

-- --------------------------------------------------------

--
-- Структура таблицы `site_storages_files`
--

CREATE TABLE IF NOT EXISTS `site_storages_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `stid` text,
  `name` varchar(255) DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `rubric` varchar(255) DEFAULT NULL,
  `uid` bigint(20) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `site_storages_files`
--


-- --------------------------------------------------------

--
-- Структура таблицы `site_storages_info`
--

CREATE TABLE IF NOT EXISTS `site_storages_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `path` text,
  `name` text,
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `site_storages_info`
--

INSERT INTO `site_storages_info` (`id`, `path`, `name`, `settings`) VALUES
(1, '/storage/users/icons/', 'Иконки для пользователей бэк-офиса', '|images|maxsize=10240|imgw=60|imgwtype=1|imgh=60|imghtype=1|exts=jpg,gif,jpeg|');

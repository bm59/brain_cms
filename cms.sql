-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Май 12 2015 г., 09:14
-- Версия сервера: 5.5.11
-- Версия PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `newcms`
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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `site_bk_users_info`
--

INSERT INTO `site_bk_users_info` (`id`, `regdate`, `login`, `pswd`, `type`, `firstname`, `secondname`, `parentname`, `email`, `picture`, `settings`) VALUES
(1, '2011-05-25', 'admin', '039a8f73e1e5dcf7663d4ef8db24ece4', 1, 'admin', 'admin', 'admin', '', 0, '|engage|notypechange|norename|nologinchange|undeletable|noswitch|help=users_mainadmin|lasttime=1429872264|'),
(2, '2015-04-03', 'user', '827ccb0eea8a706c4c34a16891f84e7b', 1, 'user', 'user', 'user', '', 0, '|engage|lasttime=1428060834|');

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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `site_bk_users_types`
--

INSERT INTO `site_bk_users_types` (`id`, `name`, `access`, `settings`) VALUES
(1, 'Администраторы', '', '|undeletable|superaccess|noedit|help=usergroups_admins|'),
(2, 'Пользователи', '|100|110|', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `site_settings`
--

INSERT INTO `site_settings` (`id`, `name`, `description`, `value`, `settings`) VALUES
(2, 'counters', 'Код счетчиков', 'код', '|type=text|notnull|undeletable|'),
(5, 'callback_email', 'Email адрес для заказа звонков (разделитель |)', 'bm59@list.ru', '|type=string|undeletable|'),
(6, 'map_address', 'Адрес на карте', 'Пермь, Писарева 56б', '|type=string|undeletable|'),
(7, 'pub_page_count', 'Количество элементов на странице типа «Лента»', '20', '|type=integer|notnull|undeletable|');

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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `site_site_data_sets`
--

INSERT INTO `site_site_data_sets` (`id`, `description`, `name`, `settings`) VALUES
(1, 'Лист (1 колонка)', 'sheet1', ''),
(4, 'Новости', 'news', ''),
(5, 'Справочник с порядком', 'sprorder', '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `site_site_data_types`
--

INSERT INTO `site_site_data_types` (`id`, `dataset`, `description`, `name`, `type`, `precedence`, `settings`) VALUES
(1, 1, 'Текст', 'text', 'CDTextEditor', 0, '|important|texttype=full|'),
(9, 4, 'Дата', 'date', 'CDDate', 0, ''),
(10, 4, 'Картинка', 'image', 'CDImage', 1, ''),
(11, 4, 'Заголовок', 'header', 'CDText', 2, '|important=1|'),
(12, 4, 'Анонс', 'short', 'CDTextArea', 3, ''),
(13, 4, 'Текст', 'text', 'CDTextEditor', 4, '|texttype=full|'),
(14, 4, 'Title страницы', 'ptitle', 'CDText', 5, ''),
(15, 4, 'Description страницы', 'pdescription', 'CDText', 6, ''),
(16, 4, 'Псеводоним ссылки', 'pseudolink', 'CDText', 7, ''),
(17, 5, 'Заголовок', 'name', 'CDText', 0, '|important=1|'),
(18, 5, 'Описание', 'description', 'CDText', 1, '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `site_site_sections`
--

INSERT INTO `site_site_sections` (`id`, `name`, `path`, `pattern`, `parent`, `precedence`, `isservice`, `keywords`, `title`, `tags`, `description`, `visible`, `settings`) VALUES
(1, 'Управление сайтом', 'control', 'PFolder', 0, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(2, 'Настройки', 'settings', 'PFolder', 1, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(3, 'Доступ', 'access', 'PFolder', 0, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(4, 'Пользователи', 'users', 'PFolder', 3, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(5, 'Группы', 'groups', 'PFolder', 3, -1, 1, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(6, 'Содержимое сайта', 'sitecontent', 'PFolder', 0, 0, 0, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(7, 'Новости', 'news', 'PNews', 6, 1, 0, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(8, 'Страничка', 'page', 'PSheet1', 6, 0, 0, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|'),
(9, 'Справочник с порядком', 'spr', 'PSprOrder', 6, 2, 0, '', '', '', '', 1, '|nopathchange|nodestination|undrop|undeletable|');

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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `site_storages_info`
--

INSERT INTO `site_storages_info` (`id`, `path`, `name`, `settings`) VALUES
(1, '/storage/users/icons/', 'Иконки для пользователей бэк-офиса', '|images|maxsize=10240|imgw=60|imgwtype=1|imgh=60|imghtype=1|exts=jpg,gif,jpeg,png|'),
(2, '/storage/site/images/', 'Изображения сайта (общее)', '|images|maxsize=10240|exts=jpg,gif,jpeg,png|'),
(3, '/storage/site/files/', 'Файлы сайта (общее)', '|maxsize=10240|');

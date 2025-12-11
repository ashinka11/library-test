-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 11 2025 г., 14:58
-- Версия сервера: 8.0.15
-- Версия PHP: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `library`
--

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `description` text,
  `text` longtext,
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id`, `user_id`, `title`, `author`, `description`, `text`, `file_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Маленький Принц', NULL, NULL, 'Когда  мне  было  шесть  лет,  в  книге  под  названием  \"Правдивые\r\nистории\",  где  рассказывалось  про  девственные  леса, я увидел однажды\r\nудивительную  картинку.   На  картинке  огромная  змея  - удав - глотала\r\nхищного зверя.\r\n     В  книге  говорилось:   \"Удав  заглатывает  свою жертву целиком, не\r\nжуя. После  этого он  уже не  может шевельнуться  и спит полгода подряд,\r\nпока не переварит пищу\".\r\n     Я  много  раздумывал   о  полной  приключений жизни джунглей и тоже\r\nнарисовал цветным карандашом свою первую картинку.  Это был мой рисунок\r\nN 1. Вот что я нарисовал. Я показал мое творение взрослым и спросил, не\r\nстрашно ли им.\r\n     - Разве шляпа страшная? - возразили мне.\r\n     А  это  была  совсем  не  шляпа.   Это  был удав, который проглотил\r\nслона.  Тогда я нарисовал  удава изнутри, чтобы взрослым было  понятнее.\r\nИм ведь всегда нужно все объяснять.  Это мой рисунок N 2.\r\n     Взрослые посоветовали мне не рисовать змей ни снаружи, ни  изнутри,\r\nа   побольше   интересоваться   географией,   историей,   арифметикой  и\r\nправописанием.   Вот  как  случилось,  что  шести  лет  я  отказался  от\r\nблестящей карьеры художника.  Потерпев неудачу с рисунками N 1 и N 2,  я\r\nутратил веру в себя.   Взрослые никогда ничего не  понимают сами, а  для\r\nдетей очень утомительно без конца им все объяснять и растолковывать.\r\n     Итак,  мне  пришлось  выбирать  другую  профессию,  и я выучился на\r\nлетчика.   Облетел  я  чуть  ли  не  весь  свет.  И география, по правде\r\nсказать, мне очень пригодилась. Я умел с первого взгляда отличить  Китай\r\nот Аризоны.  Это очень полезно, если ночью собьешься с пути.\r\n     На своем  веку я  много встречал  разных серьезных  людей.  Я долго\r\nжил среди взрослых.  Я видел  их совсем близко. И от этого,  признаться,\r\nне стал думать о них лучше.\r\n     Когда я   встречал   взрослого,  который  казался  мне  разумней  и\r\nпонятливей других,  я показывал ему свой рисунок N 1 - я его  сохранил и\r\nвсегда  носил  с  собою.  Я хотел знать,  вправду ли этот человек что-то\r\nпонимает.  Но все они отвечали мне:  \"Это шляпа\".  И я уже не говорил  с\r\nними  ни  об  удавах,  ни  о джунглях,  ни о звездах.  Я применялся к их\r\nпонятиям.  Я говорил с ними об игре в бридж и  гольф,  о  политике  и  о\r\nгалстуках.  И  взрослые  были очень довольны,  что познакомились с таким\r\nздравомыслящим человеком.', NULL, '2025-12-11 09:50:58', NULL, NULL),
(3, 1, 'Программирование на PHP в примерах и задачах', NULL, NULL, 'Язык PHP входит в топ самых популярных языков для веб-разработки, но при этом он является еще и одним из самых доступных для самостоятельного изучения языков программирования. С этой книгой освоить PHP может практически каждый, ведь в ней собраны абсолютно все знания, необходимые новичку, – от базовых понятий, истории языка и его семантики до удобно скомпонованных конкретных примеров, позволяющих не только лучше усвоить пройденный материал, но и приступить к самостоятельной реализации проектов на PHP.', NULL, '2025-12-11 10:59:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `library_access`
--

CREATE TABLE `library_access` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `granted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `library_access`
--

INSERT INTO `library_access` (`id`, `owner_id`, `user_id`, `granted_at`) VALUES
(4, 1, 2, '2025-12-11 09:26:41');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `created_at`) VALUES
(1, 'ashinka', '$2y$10$HIcoZ/tX.m5ha3K9u7P6FuXUGipedP7EZBjBlC2wspM7FGRgQzqIe', 'Анастасия', 'nstyaor@yandex.ru', '2025-12-09 19:37:10'),
(2, 'vanek1', '$2y$10$VGRBrPtNV0g8PUxbASOA5epGRHkVJVpZiKW8ezQNXrsyYJyvaUsjK', 'Иван', 'vanek1@gmail.com', '2025-12-09 19:40:03'),
(5, 'new_user', '$2y$10$WYiC83KwcxDvaP0d9.9VPuvU3hjg8JtCUgYqYiLeAMMsZTDD6M7rG', NULL, NULL, '2025-12-11 11:06:00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `library_access`
--
ALTER TABLE `library_access`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_access` (`owner_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `library_access`
--
ALTER TABLE `library_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `library_access`
--
ALTER TABLE `library_access`
  ADD CONSTRAINT `library_access_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `library_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

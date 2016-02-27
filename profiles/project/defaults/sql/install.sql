--
-- файл менеджер
--

INSERT INTO {{%elfinder_profile}} (`profile_id`, `title`, `sort`, `enabled`, `cssClass`, `rememberLastDir`, `useBrowserHistory`, `resizable`, `notifyDelay`, `loadTmbs`, `showFiles`, `validName`, `requestType`, `createDate`, `commands`, `ui`, `toolbar`, `navbar`, `cwd`, `files`) VALUES
(1, 'Основной профиль', 1, 1, '', 1, 1, 1, 100, 23, 12, '[a-z-0-9_]+', 'get', '2015-10-07 00:17:11', '["open","home","reload","up","forward","back","getfile","copy","cut","rm","edit","rename","paste","mkdir","mkfile","upload","quicklook","download","resize","archive","extract","info","view","sort","search"]', '["toolbar","tree","path","stat"]', '[["home","reload"],["back","up","forward"],["open","mkdir","mkfile","upload","download"],["getfile","quicklook","info"],["copy","cut","paste","duplicate","rename","rm"],["edit","resize"],["extract","archive"],["view","sort"],["search"]]', '["open","copy","cut","paste","duplicate","rm","info"]', '["reload","back","upload","mkdir","mkfile","paste","info"]', '["getfile","open","quicklook","download","copy","cut","paste","duplicate","rm","edit"]');

INSERT INTO {{%elfinder_dir}} (`path_id`, `profile_id`, `sort`, `path`, `alias`, `uploadMaxSize`, `acceptedName`) VALUES
(1, 1, 1, '@root/images/shared', 'Общая диретория', '40M', '[A-Za-z-0-9_.]+');

INSERT INTO {{%elfinder_uploadallow}} (`id`, `path_id`, `mimetypes`) VALUES
(1, 1, 'image/gif'),
(2, 1, 'image/jpeg'),
(3, 1, 'image/png');

INSERT INTO {{%elfinder_role}} (`id_role`, `profile_id`) VALUES
(1, 1);

--
-- маршурты
--

INSERT INTO {{%routes}} (`routes_id`, `expansion`, `alias`, `meta_title`, `meta_keywords`, `meta_description`, `meta_robots`, `date_create`) VALUES
(1, 'contents', 'home', '', '', '', '', '2015-10-04 20:43:05'),
(2, 'menu', 'glavnaya', '', '', '', '', '2015-10-04 20:44:24'),
(3, 'dictionary', 'music', '', '', '', '', '2015-10-06 11:56:25'),
(4, 'contents', 'page-2', '', '', '', '', '2015-10-06 11:58:33'),
(5, 'contents', 'page-1', '', '', '', '', '2015-10-07 00:24:08'),
(6, 'contents', 'slide-1', '', '', '', '', '2015-10-07 00:49:04'),
(7, 'contents', 'slide-2', '', '', '', '', '2015-10-07 00:51:47'),
(8, 'contents', 'slide-3', '', '', '', '', '2015-10-07 00:55:30'),
(9, 'contents', 'featured-1', '', '', '', '', '2015-10-07 01:39:09'),
(10, 'contents', 'featured-2', '', '', '', '', '2015-10-07 01:40:11'),
(11, 'contents', 'featured-3', '', '', '', '', '2015-10-07 01:40:52'),
(12, 'dictionary', 'portfolio-cat-1', '', '', '', '', '2015-10-07 19:54:32'),
(13, 'dictionary', 'portfolio-cat-2', '', '', '', '', '2015-10-07 19:55:04'),
(14, 'dictionary', 'portfolio-cat-3', '', '', '', '', '2015-10-07 19:55:51'),
(15, 'contents', 'portfolio-1', '', '', '', '', '2015-10-07 19:58:43'),
(16, 'contents', 'portfolio-2', '', '', '', '', '2015-10-07 20:01:47'),
(17, 'contents', 'portfolio-3', '', '', '', '', '2015-10-07 20:03:03'),
(18, 'contents', 'client-1', '', '', '', '', '2015-10-07 21:01:49'),
(19, 'contents', 'client-2', '', '', '', '', '2015-10-07 21:13:47'),
(20, 'contents', 'shortcodes', '', '', '', '', '2015-10-08 20:05:19'),
(21, 'menu', 'shortkodei', '', '', '', '', '2015-10-08 20:06:48'),
(22, 'contents', 'icons', '', '', '', '', '2015-10-08 20:37:55'),
(23, 'menu', 'ikonki', '', '', '', '', '2015-10-08 20:38:36'),
(24, 'contents', 'user-1', '', '', '', '', '2015-10-09 22:00:21'),
(25, 'contents', 'user-2', '', '', '', '', '2015-10-09 22:02:04'),
(26, 'contents', 'user-3', '', '', '', '', '2015-10-09 22:02:46'),
(27, 'contents', 'about-us', '', '', '', '', '2015-10-09 22:09:42'),
(28, 'menu', 'o-nas', '', '', '', '', '2015-10-09 22:10:23'),
(29, 'contents', 'service-2', '', '', '', '', '2015-10-10 08:50:05'),
(30, 'contents', 'service-1', '', '', '', '', '2015-10-10 08:51:47'),
(31, 'contents', 'service-3', '', '', '', '', '2015-10-10 08:53:47'),
(32, 'contents', 'service-4', '', '', '', '', '2015-10-10 08:55:12'),
(33, 'menu', 'nashi-uslugi', '', '', '', '', '2015-10-10 09:00:30'),
(34, 'contents', 'faq-1', '', '', '', '', '2015-10-10 10:24:30'),
(35, 'contents', 'faq-2', '', '', '', '', '2015-10-10 10:25:42'),
(36, 'menu', 'faq-alias', '', '', '', '', '2015-10-10 10:27:25'),
(37, 'contents', 'user-4', '', '', '', '', '2015-10-10 10:50:16'),
(38, 'menu', 'porfolio', '', '', '', '', '2015-10-10 15:41:32'),
(40, 'menu', 'arhitektrua', '', '', '', '', '2015-10-10 21:56:39'),
(41, 'menu', 'tehnologii', '', '', '', '', '2015-10-10 21:58:08'),
(42, 'menu', 'fotografiya', '', '', '', '', '2015-10-10 21:58:42'),
(43, 'menu', 'blog', '', '', '', '', '2015-10-11 18:52:57'),
(44, 'contents', 'blog-3', '', '', '', '', '2015-10-11 19:52:13'),
(45, 'dictionary', 'blog-cat-2', '', '', '', '', '2015-10-11 19:53:46'),
(46, 'dictionary', 'blog-cat-3', '', '', '', '', '2015-10-11 19:54:24'),
(47, 'dictionary', 'blog-cat-4', '', '', '', '', '2015-10-11 19:55:18'),
(48, 'dictionary', 'blog-cat-5', '', '', '', '', '2015-10-11 19:56:42'),
(49, 'contents', 'blog-4', '', '', '', '', '2015-10-11 19:58:17'),
(50, 'contents', 'blog-6', '', '', '', '', '2015-10-11 19:59:44'),
(58, 'menu', 'muzeika', '', '', '', '', '2015-10-11 22:05:16'),
(59, 'menu', 'tehnologiya', '', '', '', '', '2015-10-11 22:07:14'),
(60, 'menu', 'razvlekatelnaya-programma', '', '', '', '', '2015-10-11 22:07:51'),
(61, 'menu', 'zhivopis', '', '', '', '', '2015-10-11 22:08:22'),
(62, 'menu', 'fotografiya-cat', '', '', '', '', '2015-10-11 22:09:02'),
(63, 'contents', 'page-3', '', '', '', '', '2015-10-12 21:54:45'),
(64, 'menu', 'kontaktei', '', '', '', '', '2015-10-12 21:55:34');

--
-- типы материала
--

INSERT INTO {{%content_type}} (`bundle`, `expansion`, `title`, `description`, `param`, `date_create`) VALUES
('basepage', 'contents', 'Базовая страница', 'Статическая страница сайта ', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 14:23:21'),
('blog', 'contents', 'Блог', 'Набор статей для вашей компании ', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 14:04:26'),
('blogcat', 'dictionary', 'Категории блога', 'Данные термины содержат статьи типа материала &quot;Блог&quot;', 'a:4:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:6:"length";s:3:"255";}', '2015-10-04 14:08:09'),
('clients', 'contents', 'Наши клиенты', 'Список клиентов вашей компании ', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 20:41:30'),
('employees', 'contents', 'Сотрудники', 'Перечень сотрудников работающих в вашей компании', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:27:"Имя сотрудника";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 19:03:41'),
('faqtype', 'contents', 'FAQ', 'Часто задаваемые вопросы', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:12:"Вопрос";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 14:14:01'),
('featured', 'contents', 'Преимущества', 'Блоки расположенные на  главной странице', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 13:05:37'),
('homeslider', 'contents', 'Слайдер главной', 'Ротатор баннеров на главной странице', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 12:34:51'),
('portfolio', 'contents', 'Портфолио', 'Работы выполняемые вашей фирмой', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 13:15:25'),
('portfoliocat', 'dictionary', 'Категории порфолио', 'Данный термин содержит материалы типа &quot;портфолио&quot;', 'a:4:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:6:"length";s:3:"255";}', '2015-10-04 13:24:49'),
('services', 'contents', 'Наши услуги', 'Список услуг которая оказывает ваша компания', 'a:5:{s:9:"multilang";s:1:"0";s:7:"enabled";s:1:"1";s:5:"title";s:18:"Заголовок";s:4:"home";s:1:"0";s:6:"length";s:3:"255";}', '2015-10-04 19:19:00');

--
-- контент
--

INSERT INTO {{%contents}} (`contents_id`, `expansion`, `bundle`, `routes_id`, `enabled`, `home`, `sort`, `id_lang`, `id_user`, `time_active`, `time_inactive`, `date_create`) VALUES
(1, 'contents', 'basepage', 1, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-04 20:43:05'),
(2, 'contents', 'blog', 4, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-06 11:58:33'),
(3, 'contents', 'blog', 5, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-07 00:24:08'),
(4, 'contents', 'homeslider', 6, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-07 00:49:04'),
(5, 'contents', 'homeslider', 7, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-07 00:51:47'),
(6, 'contents', 'homeslider', 8, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-07 00:55:30'),
(7, 'contents', 'featured', 9, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-07 01:39:09'),
(8, 'contents', 'featured', 10, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-07 01:40:11'),
(9, 'contents', 'featured', 11, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-07 01:40:52'),
(10, 'contents', 'portfolio', 15, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-07 19:58:43'),
(11, 'contents', 'portfolio', 16, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-07 20:01:47'),
(12, 'contents', 'portfolio', 17, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-07 20:03:03'),
(13, 'contents', 'clients', 18, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-07 21:01:49'),
(14, 'contents', 'clients', 19, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-07 21:13:47'),
(15, 'contents', 'basepage', 20, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-08 20:05:19'),
(16, 'contents', 'basepage', 22, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-08 20:37:55'),
(17, 'contents', 'employees', 24, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-09 22:00:21'),
(18, 'contents', 'employees', 25, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-09 22:02:04'),
(19, 'contents', 'employees', 26, 1, 0, 4, 0, 1, NULL, NULL, '2015-10-09 22:02:46'),
(20, 'contents', 'basepage', 27, 1, 0, 4, 0, 1, NULL, NULL, '2015-10-09 22:09:42'),
(21, 'contents', 'services', 29, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-10 08:50:05'),
(22, 'contents', 'services', 30, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-10 08:51:47'),
(23, 'contents', 'services', 31, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-10 08:53:47'),
(24, 'contents', 'services', 32, 1, 0, 4, 0, 1, NULL, NULL, '2015-10-10 08:55:12'),
(25, 'contents', 'faqtype', 34, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-10 10:24:30'),
(26, 'contents', 'faqtype', 35, 1, 0, 2, 0, 1, NULL, NULL, '2015-10-10 10:25:42'),
(27, 'contents', 'employees', 37, 1, 0, 1, 0, 1, NULL, NULL, '2015-10-10 10:50:16'),
(28, 'contents', 'blog', 44, 1, 0, 3, 0, 1, NULL, NULL, '2015-10-11 19:52:13'),
(29, 'contents', 'blog', 49, 1, 0, 4, 0, 1, NULL, NULL, '2015-10-11 19:58:17'),
(30, 'contents', 'blog', 50, 1, 0, 5, 0, 1, NULL, NULL, '2015-10-11 19:59:44'),
(31, 'contents', 'basepage', 63, 1, 0, 5, 0, 1, NULL, NULL, '2015-10-12 21:54:45');

--
-- термины словаря
--

INSERT INTO {{%dictionary_term}} (`term_id`, `parent`, `routes_id`, `expansion`, `bundle`, `sort`, `enabled`, `id_lang`, `time_active`, `time_inactive`, `date_create`) VALUES
(1, 0, 3, 'dictionary', 'blogcat', 1, 1, 0, NULL, NULL, '2015-10-06 11:56:25'),
(2, 0, 12, 'dictionary', 'portfoliocat', 1, 1, 0, NULL, NULL, '2015-10-07 19:54:32'),
(3, 0, 13, 'dictionary', 'portfoliocat', 2, 1, 0, NULL, NULL, '2015-10-07 19:55:04'),
(4, 0, 14, 'dictionary', 'portfoliocat', 3, 1, 0, NULL, NULL, '2015-10-07 19:55:51'),
(5, 0, 45, 'dictionary', 'blogcat', 2, 1, 0, NULL, NULL, '2015-10-11 19:53:46'),
(6, 0, 46, 'dictionary', 'blogcat', 3, 1, 0, NULL, NULL, '2015-10-11 19:54:24'),
(7, 0, 47, 'dictionary', 'blogcat', 4, 1, 0, NULL, NULL, '2015-10-11 19:55:18'),
(8, 0, 48, 'dictionary', 'blogcat', 5, 1, 0, NULL, NULL, '2015-10-11 19:56:42');

--
-- сортировка терминов в группе
--

INSERT INTO {{%content_term_sort}} (`contents_id`, `term_id`, `sort`) VALUES
(2, 1, 2),
(3, 1, 1),
(3, 5, 1),
(10, 2, 1),
(11, 2, 2),
(11, 3, 1),
(12, 4, 1),
(28, 1, 3),
(28, 6, 1),
(29, 7, 1),
(30, 8, 1);

--
-- типы полей
--

INSERT INTO {{%fields}} (`field_id`, `field_name`, `type`, `date_create`) VALUES
(1, 'title', 'title', '2015-10-04 12:34:51'),
(2, 'images', 'images', '2015-10-04 12:47:27'),
(3, 'timespeed', 'number', '2015-10-04 12:51:27'),
(4, 'body', 'body', '2015-10-04 12:55:46'),
(5, 'icon', 'text', '2015-10-04 13:07:44'),
(6, 'desc', 'textarea', '2015-10-04 13:11:05'),
(7, 'listworks', 'text', '2015-10-04 13:23:12'),
(8, 'portfoliocat', 'term', '2015-10-04 13:26:07'),
(9, 'blogcat', 'term', '2015-10-04 14:09:26'),
(10, 'reply', 'textarea', '2015-10-04 14:15:00'),
(11, 'position', 'text', '2015-10-04 19:04:27'),
(12, 'photo', 'images', '2015-10-04 19:26:56'),
(13, 'username', 'text', '2015-10-04 19:27:43');

--
-- поля компаненты
--

INSERT INTO {{%field_exp}} (`field_exp_id`, `field_id`, `field_name`, `title`, `prompt`, `expansion`, `bundle`, `locked`, `active`, `sort`, `many_value`, `widget_name`, `param`, `widget_param`) VALUES
(1, 1, 'title', 'Заголовок', NULL, 'contents', 'homeslider', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(2, 2, 'images', 'Фоновое изображение', 'Изображение служит подложкой слайда', 'contents', 'homeslider', 0, 1, 2, 1, 'imgbox', 'a:7:{s:8:"required";s:1:"0";s:11:"pathDefault";N;s:8:"minWidth";s:4:"1900";s:8:"maxWidth";s:4:"3000";s:9:"minHeight";s:3:"470";s:9:"maxHeight";s:4:"2000";s:5:"types";s:14:"jpg, png, jpeg";}', NULL),
(3, 3, 'timespeed', 'Время воспроизведения слайда', 'время воспроизведения слайда в миллисекундах', 'contents', 'homeslider', 0, 1, 3, 1, 'input', 'a:4:{s:3:"max";s:6:"100000";s:3:"min";s:2:"10";s:8:"required";s:1:"1";s:5:"round";s:1:"0";}', NULL),
(4, 4, 'body', 'Текст баннера', 'HTML атрибуты: data-x=0 -  позиция по x; data-y=170  -  позиция по y; data-speed=400 - скорость воспроизведения; data-start=800 - время начало воспроизведения; data-easing=easeOutExpo - эффект перехода;', 'contents', 'homeslider', 0, 1, 4, 1, 'body', 'a:5:{s:7:"reqfull";s:1:"0";s:7:"reqprev";s:1:"0";s:6:"filter";a:1:{i:0;s:8:"fullHtml";}s:13:"filterDefault";s:8:"fullHtml";s:7:"listtag";s:21:"script, style, iframe";}', 'a:2:{s:10:"enableprev";s:1:"0";s:10:"enablefull";s:1:"1";}'),
(5, 1, 'title', 'Заголовок', NULL, 'contents', 'featured', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(6, 5, 'icon', 'CSS  класс иконки', '', 'contents', 'featured', 0, 1, 2, 1, 'input', 'a:3:{s:3:"max";s:3:"255";s:3:"min";s:1:"1";s:8:"required";s:1:"1";}', NULL),
(7, 6, 'desc', 'Анонс', '', 'contents', 'featured', 0, 1, 3, 1, 'input', 'a:3:{s:3:"max";s:3:"150";s:3:"min";s:1:"1";s:8:"required";s:1:"1";}', NULL),
(8, 1, 'title', 'Заголовок', NULL, 'contents', 'portfolio', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(9, 2, 'images', 'Изображения', '', 'contents', 'portfolio', 0, 1, 2, 12, 'imgbox', 'a:7:{s:8:"required";s:1:"1";s:11:"pathDefault";N;s:8:"minWidth";s:3:"800";s:8:"maxWidth";s:4:"3000";s:9:"minHeight";s:3:"400";s:9:"maxHeight";s:4:"2000";s:5:"types";s:14:"jpg, png, jpeg";}', NULL),
(10, 4, 'body', 'Описание работы', '', 'contents', 'portfolio', 0, 1, 5, 1, 'body', 'a:5:{s:7:"reqfull";s:1:"0";s:7:"reqprev";s:1:"0";s:6:"filter";a:3:{i:0;s:9:"stripHtml";i:1;s:10:"filterHtml";i:2;s:8:"fullHtml";}s:13:"filterDefault";s:9:"stripHtml";s:7:"listtag";s:21:"script, style, iframe";}', 'a:2:{s:10:"enableprev";s:1:"0";s:10:"enablefull";s:1:"1";}'),
(11, 7, 'listworks', 'Подробная информация о проекте', '', 'contents', 'portfolio', 0, 1, 4, 0, 'input', 'a:3:{s:3:"max";s:3:"255";s:3:"min";s:1:"1";s:8:"required";s:1:"1";}', NULL),
(12, 1, 'title', 'Заголовок', NULL, 'dictionary', 'portfoliocat', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(13, 8, 'portfoliocat', 'Категория портфолио', '', 'contents', 'portfolio', 0, 1, 3, 1, 'tree', 'a:2:{s:8:"required";s:1:"1";s:10:"dictionary";s:12:"portfoliocat";}', NULL),
(14, 1, 'title', 'Заголовок', NULL, 'contents', 'blog', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(15, 2, 'images', 'Изображение', '', 'contents', 'blog', 0, 1, 2, 12, 'imgbox', 'a:7:{s:8:"required";s:1:"1";s:11:"pathDefault";N;s:8:"minWidth";s:3:"800";s:8:"maxWidth";s:4:"3000";s:9:"minHeight";s:3:"400";s:9:"maxHeight";s:4:"2000";s:5:"types";s:14:"jpg, png, jpeg";}', NULL),
(16, 4, 'body', 'Текст статьи', '', 'contents', 'blog', 0, 1, 4, 1, 'body', 'a:5:{s:7:"reqfull";s:1:"1";s:7:"reqprev";s:1:"1";s:6:"filter";a:3:{i:0;s:9:"stripHtml";i:1;s:10:"filterHtml";i:2;s:8:"fullHtml";}s:13:"filterDefault";s:8:"fullHtml";s:7:"listtag";s:21:"script, style, iframe";}', 'a:2:{s:10:"enableprev";s:1:"0";s:10:"enablefull";s:1:"1";}'),
(17, 1, 'title', 'Заголовок', NULL, 'dictionary', 'blogcat', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(18, 9, 'blogcat', 'Категория блога', '', 'contents', 'blog', 0, 1, 3, 1, 'tree', 'a:2:{s:8:"required";s:1:"1";s:10:"dictionary";s:7:"blogcat";}', NULL),
(19, 1, 'title', 'Вопрос', NULL, 'contents', 'faqtype', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(20, 10, 'reply', 'Ответ на вопрос', '', 'contents', 'faqtype', 0, 1, 2, 1, 'input', 'a:3:{s:3:"max";s:3:"500";s:3:"min";s:1:"1";s:8:"required";s:1:"1";}', NULL),
(21, 1, 'title', 'Заголовок', NULL, 'contents', 'basepage', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(22, 4, 'body', 'Текст страницы', '', 'contents', 'basepage', 0, 1, 2, 1, 'body', 'a:5:{s:7:"reqfull";s:1:"0";s:7:"reqprev";s:1:"0";s:6:"filter";a:3:{i:0;s:9:"stripHtml";i:1;s:10:"filterHtml";i:2;s:8:"fullHtml";}s:13:"filterDefault";s:8:"fullHtml";s:7:"listtag";s:21:"script, style, iframe";}', 'a:2:{s:10:"enableprev";s:1:"0";s:10:"enablefull";s:1:"1";}'),
(23, 1, 'title', 'Имя сотрудника', NULL, 'contents', 'employees', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(24, 11, 'position', 'Должность', '', 'contents', 'employees', 0, 1, 2, 1, 'input', 'a:3:{s:3:"max";s:3:"255";s:3:"min";s:1:"1";s:8:"required";s:1:"1";}', NULL),
(25, 2, 'images', 'Фото сотрудрика', '', 'contents', 'employees', 0, 1, 3, 1, 'imgbox', 'a:7:{s:8:"required";s:1:"0";s:11:"pathDefault";s:27:"/images/shared/about-01.jpg";s:8:"minWidth";s:3:"200";s:8:"maxWidth";s:4:"3000";s:9:"minHeight";s:3:"200";s:9:"maxHeight";s:4:"2000";s:5:"types";s:14:"jpg, png, jpeg";}', NULL),
(26, 6, 'desc', 'О сотруднике', '', 'contents', 'employees', 0, 1, 4, 1, 'input', 'a:3:{s:3:"max";s:3:"500";s:3:"min";s:1:"1";s:8:"required";s:1:"0";}', NULL),
(27, 1, 'title', 'Заголовок', NULL, 'contents', 'services', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(28, 5, 'icon', 'CSS  класс иконки', '', 'contents', 'services', 0, 1, 2, 1, 'input', 'a:3:{s:3:"max";s:3:"255";s:3:"min";s:1:"1";s:8:"required";s:1:"1";}', NULL),
(29, 4, 'body', 'Описание услуги', '', 'contents', 'services', 0, 1, 3, 1, 'body', 'a:5:{s:7:"reqfull";s:1:"0";s:7:"reqprev";s:1:"1";s:6:"filter";a:3:{i:0;s:9:"stripHtml";i:1;s:10:"filterHtml";i:2;s:8:"fullHtml";}s:13:"filterDefault";s:10:"filterHtml";s:7:"listtag";s:21:"script, style, iframe";}', 'a:2:{s:10:"enableprev";s:1:"0";s:10:"enablefull";s:1:"1";}'),
(30, 12, 'photo', 'Фото заказчика', '', 'contents', 'portfolio', 0, 1, 6, 1, 'imgbox', 'a:7:{s:8:"required";s:1:"0";s:11:"pathDefault";s:27:"/images/shared/about-01.jpg";s:8:"minWidth";s:3:"800";s:8:"maxWidth";s:4:"3000";s:9:"minHeight";s:3:"400";s:9:"maxHeight";s:4:"2000";s:5:"types";s:14:"jpg, png, jpeg";}', NULL),
(31, 13, 'username', 'Имя заказчика', '', 'contents', 'portfolio', 0, 1, 7, 1, 'input', 'a:3:{s:3:"max";s:3:"255";s:3:"min";s:1:"1";s:8:"required";s:1:"0";}', NULL),
(32, 6, 'desc', 'Текст отзыва', '', 'contents', 'portfolio', 0, 1, 8, 1, 'input', 'a:3:{s:3:"max";s:3:"300";s:3:"min";s:1:"1";s:8:"required";s:1:"0";}', NULL),
(33, 1, 'title', 'Заголовок', NULL, 'contents', 'clients', 1, 1, 1, 1, 'input', 'a:2:{s:6:"length";s:3:"255";s:8:"required";i:1;}', NULL),
(34, 2, 'images', 'Логотип компании', '', 'contents', 'clients', 0, 1, 2, 1, 'imgbox', 'a:7:{s:8:"required";s:1:"1";s:11:"pathDefault";N;s:8:"minWidth";s:3:"180";s:8:"maxWidth";s:4:"3000";s:9:"minHeight";s:2:"80";s:9:"maxHeight";s:4:"2000";s:5:"types";s:14:"jpg, png, jpeg";}', NULL);

--
-- представление полей
--

INSERT INTO {{%content_type_view}} (`view_name`, `expansion`, `bundle`, `entry_type`, `enabled`, `group_name`, `sort`, `field_exp_id`, `mode`, `show_label`, `class_wrapper`, `tag_wrapper`, `multiple_size`, `multiple_start`, `field_view`, `field_view_param`) VALUES
('fbasepage', 'contents', 'basepage', 'contents', 1, 'base', NULL, 21, 1, 0, '', 'h1', NULL, NULL, 'default', NULL),
('fbasepagebody', 'contents', 'basepage', 'contents', 1, 'base', NULL, 22, 1, 0, '', 'div', NULL, NULL, 'default', NULL),
('fbodyblog', 'contents', 'blog', 'contents', 1, 'base', 3, 16, 1, 0, '', '', NULL, NULL, 'default', NULL),
('fbodyport', 'contents', 'portfolio', 'contents', 1, 'base', 2, 10, 1, 1, '', '', NULL, NULL, 'default', NULL),
('fbodyservices', 'contents', 'services', 'contents', 1, 'base', 2, 29, 1, 0, '', 'div', NULL, NULL, 'default', NULL),
('fimagesblog', 'contents', 'blog', 'contents', 1, 'base', 2, 15, 1, 0, '', '', NULL, NULL, 'resize', 'a:2:{s:5:"width";s:3:"860";s:6:"height";s:3:"320";}'),
('fimagespor', 'contents', 'portfolio', 'contents', 1, 'base', 1, 9, 1, 0, '', '', NULL, NULL, 'default', 'a:2:{s:5:"width";s:3:"775";s:6:"height";s:3:"430";}'),
('flistworkspot', 'contents', 'portfolio', 'contents', 1, 'base', 3, 11, 1, 1, '', '', NULL, NULL, 'default', NULL),
('ftitleblog', 'dictionary', 'blogcat', 'contents', 1, 'base', NULL, 17, 1, 0, 'page-title', 'h1', NULL, NULL, 'default', NULL),
('ftitleblogp', 'contents', 'blog', 'contents', 1, 'base', 1, 14, 1, 0, 'page-title', 'h1', NULL, NULL, 'default', NULL),
('ftitleport', 'dictionary', 'portfoliocat', 'contents', 1, 'base', NULL, 12, 1, 0, 'page-title', 'h1', NULL, NULL, 'default', NULL),
('ftitleportf', 'contents', 'portfolio', 'contents', 1, 'base', NULL, 8, 1, 0, 'page-title', 'h1', NULL, NULL, 'default', NULL),
('ftitleservice', 'contents', 'services', 'contents', 1, 'base', 1, 27, 0, 0, '', 'h3', NULL, NULL, 'default', NULL),
('ftitleservicef', 'contents', 'services', 'contents', 1, 'base', 1, 27, 1, 0, '', 'h1', NULL, NULL, 'default', NULL),
('pbodyblog', 'contents', 'blog', 'contents', 1, 'base', 3, 16, 0, 0, '', '', NULL, NULL, 'prev', 'a:3:{s:6:"length";s:3:"300";s:6:"prefix";s:3:"...";s:8:"stripTag";s:1:"1";}'),
('pbodyservice', 'contents', 'services', 'contents', 1, 'base', 3, 29, 0, 0, '', 'p', NULL, NULL, 'prev', 'a:3:{s:6:"length";s:2:"50";s:6:"prefix";s:3:"...";s:8:"stripTag";s:1:"1";}'),
('pcategorypor', 'contents', 'portfolio', 'contents', 1, 'base', 3, 13, 0, 0, '', 'span', NULL, NULL, 'default', NULL),
('pdesfaq', 'contents', 'faqtype', 'contents', 1, 'base', 2, 20, 0, 0, '', 'p', NULL, NULL, 'default', NULL),
('piconservice', 'contents', 'services', 'contents', 1, 'base', 2, 28, 0, 0, '', '', NULL, NULL, 'default', NULL),
('pimagesblog', 'contents', 'blog', 'contents', 1, 'base', 2, 15, 0, 0, '', '', NULL, NULL, 'resize', 'a:2:{s:5:"width";s:3:"860";s:6:"height";s:3:"320";}'),
('pimagespro', 'contents', 'portfolio', 'contents', 1, 'base', 1, 9, 0, 0, '', '', 1, NULL, 'resize', 'a:2:{s:5:"width";s:3:"420";s:6:"height";s:3:"300";}'),
('ptitleblog', 'contents', 'blog', 'contents', 1, 'base', 1, 14, 0, 0, '', '', NULL, NULL, 'default', NULL),
('ptitlefaq', 'contents', 'faqtype', 'contents', 1, 'base', 1, 19, 0, 0, '', '', NULL, NULL, 'default', NULL),
('ptitlepor', 'contents', 'portfolio', 'contents', 1, 'base', 2, 8, 0, 0, '', 'h5', NULL, NULL, 'default', NULL);

--
-- таблицы полей 
--

CREATE TABLE {{%field_title_title}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `title_value` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_body_body}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_prev` text COMMENT 'Анонс',
  `text_full` longtext COMMENT 'Текст полностью',
  `text_format` varchar(50) DEFAULT NULL COMMENT 'Формат текста',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_text_username}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL COMMENT 'Значение текстового поля',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_text_position}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL COMMENT 'Значение текстового поля',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_text_listworks}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL COMMENT 'Значение текстового поля',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_text_icon}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL COMMENT 'Значение текстового поля',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_textarea_reply}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_value` longtext COMMENT 'Текст',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_textarea_desc}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `text_value` longtext COMMENT 'Текст',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_term_portfoliocat}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `term_id` int(11) DEFAULT NULL COMMENT 'id термина словаря',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY (`term_id`) REFERENCES {{%dictionary_term}} (`term_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_term_blogcat}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `term_id` int(11) DEFAULT NULL COMMENT 'id термина словаря',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY (`term_id`) REFERENCES {{%dictionary_term}} (`term_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_number_timespeed}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `number_value` decimal(5,0) DEFAULT NULL COMMENT 'Значение числового поля',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_images_photo}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `path_image` varchar(6000) DEFAULT NULL COMMENT 'Путь к изображению',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%field_images_images}} (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `path_image` varchar(6000) DEFAULT NULL COMMENT 'Путь к изображению',
   PRIMARY KEY (`id`),
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (field_exp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Данные полей
--

INSERT INTO {{%field_title_title}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `title_value`) VALUES
(2, 17, 1, 0, 'Музыка'),
(21, 12, 2, 0, 'Архитектура'),
(22, 12, 3, 0, 'Технологии'),
(23, 12, 4, 0, 'Фотография'),
(28, 33, 14, 0, 'Клиент 2'),
(29, 33, 13, 0, 'Клиент 1'),
(30, 5, 7, 0, 'Резиновый (Flexible)'),
(31, 5, 8, 0, 'Адаптивный (Responsive)'),
(32, 5, 9, 0, ' Сверх четкий (Retina Ready)'),
(40, 21, 15, 0, 'Шорткоды'),
(50, 8, 12, 0, 'Работа 3'),
(55, 21, 1, 0, 'Maze CMS гибкая и простая система управления сайтом'),
(56, 23, 17, 0, 'Николай Константинович'),
(57, 23, 18, 0, 'Николай Константинович'),
(58, 23, 19, 0, 'Николай Константинович'),
(66, 27, 22, 0, 'Разработка'),
(67, 27, 21, 0, 'Веб дизайн'),
(69, 27, 24, 0, 'Веб хостинг'),
(71, 21, 20, 0, 'О нас'),
(72, 19, 25, 0, 'Предсознательное стабильно'),
(74, 19, 26, 0, 'Предсознательное стабильно'),
(75, 23, 27, 0, 'Николай Константинович'),
(79, 8, 10, 0, 'Работа 1'),
(80, 8, 11, 0, 'Работа 2'),
(85, 17, 5, 0, 'Технологии'),
(86, 17, 6, 0, 'Развлекательная программа'),
(87, 17, 7, 0, 'Живопись'),
(88, 17, 8, 0, 'Фотография'),
(89, 14, 3, 0, 'Почему изоморфна дивергенция векторного поля?'),
(90, 14, 28, 0, 'Экспериментальный  Пуассона?'),
(91, 14, 29, 0, 'Убывающий лист Мёбиуса'),
(92, 14, 30, 0, 'Нормальный скачок функции глазами современников'),
(94, 14, 2, 0, 'Почему иррационален разрыв функции?'),
(98, 21, 31, 0, 'Контакты'),
(99, 27, 23, 0, 'SEO продвижение'),
(100, 21, 16, 0, 'Иконки'),
(105, 1, 4, 0, 'Слайд 1'),
(109, 1, 5, 0, 'Слайд 2'),
(110, 1, 6, 0, 'Слайд 3');

INSERT INTO {{%field_text_username}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_value`) VALUES
(6, 31, 12, 0, ''),
(16, 31, 10, 0, 'Николай Константинович'),
(17, 31, 11, 0, 'Николай Константинович');

INSERT INTO {{%field_text_position}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_value`) VALUES
(1, 24, 17, 0, 'Программист'),
(2, 24, 18, 0, 'Верстальщик'),
(3, 24, 19, 0, 'Дизайнер'),
(4, 24, 27, 0, 'Директор');

INSERT INTO {{%field_text_listworks}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_value`) VALUES
(21, 11, 12, 0, 'Branding'),
(22, 11, 12, 0, 'HTML5 / CSS3'),
(23, 11, 12, 0, 'nformation Architecture'),
(24, 11, 12, 0, 'Programming'),
(61, 11, 10, 0, 'Branding'),
(62, 11, 10, 0, 'HTML5 / CSS3'),
(63, 11, 10, 0, 'Information Architecture'),
(64, 11, 10, 0, 'Programming'),
(65, 11, 11, 0, 'Branding'),
(66, 11, 11, 0, 'HTML5 / CSS3'),
(67, 11, 11, 0, 'nformation Architecture'),
(68, 11, 11, 0, 'Programming');

INSERT INTO {{%field_text_icon}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_value`) VALUES
(4, 6, 7, 0, 'icon-wrench'),
(5, 6, 8, 0, 'icon-screenshot'),
(6, 6, 9, 0, 'icon-magic'),
(8, 28, 22, 0, 'icon-folder-open-alt'),
(9, 28, 21, 0, 'icon-laptop'),
(11, 28, 24, 0, 'icon-hdd'),
(13, 28, 23, 0, ' icon-rocket');

INSERT INTO {{%field_textarea_reply}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_value`) VALUES
(1, 20, 25, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.'),
(3, 20, 26, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.');

INSERT INTO {{%field_textarea_desc}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_value`) VALUES
(7, 7, 7, 0, 'позволяет контролировать размер, порядок и выравнивание элементов по нескольким осям, распределение свободного места между элементами и многое другое.'),
(8, 7, 8, 0, 'Нужны для отображения макета, оптимизированного под разрешение, с которого в данный момент этот сайт смотрится. Это часть стандарта CSS'),
(9, 7, 9, 0, 'вы получаете двойной объем пикселей на то же пространство'),
(12, 32, 12, 0, ''),
(15, 26, 17, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.'),
(16, 26, 18, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.'),
(17, 26, 19, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.'),
(22, 26, 27, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.'),
(26, 32, 10, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.'),
(27, 32, 11, 0, 'Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.');

INSERT INTO {{%field_term_portfoliocat}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `term_id`) VALUES
(6, 13, 12, 0, 4),
(16, 13, 10, 0, 2),
(17, 13, 11, 0, 3);

INSERT INTO {{%field_term_blogcat}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `term_id`) VALUES
(16, 18, 3, 0, 5),
(17, 18, 28, 0, 6),
(18, 18, 29, 0, 7),
(19, 18, 30, 0, 8),
(21, 18, 2, 0, 1);

INSERT INTO {{%field_number_timespeed}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `number_value`) VALUES
(9, 3, 4, 0, '300'),
(13, 3, 5, 0, '300'),
(14, 3, 6, 0, '300');

INSERT INTO {{%field_images_photo}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `path_image`) VALUES
(5, 30, 10, 0, '/images/shared/blog-01a.jpg');

INSERT INTO {{%field_images_images}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `path_image`) VALUES
(20, 34, 14, 0, '/images/shared/logo-01.png'),
(21, 34, 13, 0, '/images/shared/logo-01.png'),
(25, 9, 12, 0, '/images/shared/blog-03a.jpg'),
(28, 25, 17, 0, '/images/shared/about-01.jpg'),
(29, 25, 18, 0, '/images/shared/about-01.jpg'),
(30, 25, 19, 0, '/images/shared/about-01.jpg'),
(42, 9, 10, 0, '/images/shared/blog-01a.jpg'),
(43, 9, 10, 0, '/images/shared/blog-02a.jpg'),
(44, 9, 10, 0, '/images/shared/blog-03a.jpg'),
(45, 9, 11, 0, '/images/shared/blog-02a.jpg'),
(56, 15, 3, 0, '/images/shared/blog-02a.jpg'),
(57, 15, 3, 0, '/images/shared/blog-01a.jpg'),
(58, 15, 3, 0, '/images/shared/blog-03a.jpg'),
(59, 15, 28, 0, '/images/shared/blog-02a.jpg'),
(60, 15, 29, 0, '/images/shared/blog-03a.jpg'),
(61, 15, 30, 0, '/images/shared/blog-01a.jpg'),
(66, 15, 2, 0, '/images/shared/blog-01a.jpg'),
(67, 15, 2, 0, '/images/shared/blog-02a.jpg'),
(68, 15, 2, 0, '/images/shared/blog-03a.jpg'),
(73, 2, 4, 0, '/images/shared/slider-02.jpg'),
(77, 2, 5, 0, '/images/shared/slider-09.png'),
(78, 2, 6, 0, '/images/shared/slider-03.png');


INSERT INTO {{%field_body_body}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_prev`, `text_full`, `text_format`) VALUES
(19, 22, 15, 0, '', '<h3 class="headline">Blockquote</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Maecenas dolor est, interdum a euismod eu, accumsan posuere nisl. Nam sed iaculis massa. Sed nisl lectus, tempor sed euismod quis, sollicitudin nec est. Suspendisse dignissim bibendum tempor. Nam erat felis, commodo sed semper commodo vel mauris suspendisse dignissim bibendum tempus.</p>\r\n<blockquote>Mauris aliquet ultricies ante, non faucibus ante gravida sed. Sed ultrices pellentesque purus, vulputate volutpat ipsum hendrerit sed neque sed sapien rutrum laoreet justo ultrices. In pellentesque lorem condimentum dui morbi pulvinar dui non quam pretium ut lacinia tortor.</blockquote>\r\n<p>In ut odio libero, at vulputate urna. Nulla tristique mi a massa convallis cursus. Nulla eu mi magna. Etiam suscipit commodo gravida. Cras suscipit, quam vitae adipiscing faucibus, risus nibh laoreet odio, a porttitor metus eros ut enim. Morbi augue velit, tempus mattis dignissim nec, porta sed risus. Donec eget magna eu lorem tristique pellentesque eget eu dui. Fusce lacinia tempor malesuada. Ut lacus sapien, placerat a ornare nec, elementum sit amet felis. Maecenas pretium hendrerit fermentum.</p>\r\n<div style="margin: 35px 0;"></div>\r\n<h3 class="headline">Dropcap</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n<p><span class="dropcap">D</span> onec varius condimentum augue, nec mollis risus egestas sit amet. Etiam elit est, tincidunt non tincidunt sit amet, mollis vitae orci. Suspendisse sit amet turpis quam. Sed varius magna nec felis faucibus blandit. Quisque at gravida ante. Phasellus sagittis aliquam sodales. Donec a tortor vitae dolor sagittis aliquam eu id lectus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus ac dapibus nisl. Proin in tellus non velit sagittis viverra vel sit amet dui. Cras augue ante, tincidunt consequat ultricies ultricies, tempor a elit. Vestibulum feugiat dapibus tempus. Integer suscipit ipsum sem fermentum bibendum. Vestibulum quam neque, pretium eu consectetur quis, iaculis et massa. Sed consectetur erat fermentum purus condimentum aliquam.</p>\r\n<div style="margin: 35px 0;"></div>\r\n<h3 class="headline">Highlights</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Aliquam sed leo leo, at aliquam felis. Sed sed purus ac <span class="highlight gray">lacus faucibus placerat</span>. Donec hendrerit dapibus justo, bibendum iaculis sem vehicula sed. Fusce dolor orci, eleifend quis dignissim non, tristique sit amet lorem ipsum dolor sit amet elit. Maecenas semper venenatis felis eu <span class="highlight color">hendrerit in tincidunt</span> vehicula. Cras dictum mi nec dolor justo lacinia. Nunc at sem lectus, quis <span class="highlight light">aliquam metus</span>. Nam sagittis est at erat tincidunt ut porttitor eros viverra</p>\r\n<!-- Container / End -->\r\n<div style="margin: 35px 0;"></div>\r\n<!-- Container -->\r\n<h3 class="headline">Tooltips</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Maecenas dolor est, interdum a euismod eu, <a href="#" class="tooltip top" title="First Tooltip">tooltip from top</a> nisl. Nam sed iaculis massa. Sed nisl lectus, tempor sed euismod quis, sollicitudin est <a href="#" class="tooltip right" title="Second Tooltip">tooltip from right</a> dignissim bibendum <a href="#" class="tooltip left" title="Third Tooltip">tooltip from left</a> nam erat felis, commodo sed semper commodo vel mauris <a href="#" class="tooltip bottom" title="Fourth Tooltip">tooltip from bottom</a> bibendum tempus.</p>\r\n<!-- Container / End -->\r\n<div style="margin: 35px 0;"></div>\r\n<!-- Container -->\r\n<h3 class="headline">List Styles</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n<div class="four columns">\r\n<ul class="list-1">\r\n<li>Check List lorem ipsum aperia</li>\r\n<li>Check List lorem ipsum aperia</li>\r\n<li>Check List lorem ipsum aperia</li>\r\n<li>Check List lorem ipsum aperia</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="list-2">\r\n<li>Sign List - lorem ipsum aperia</li>\r\n<li>Sign List - lorem ipsum aperia</li>\r\n<li>Sign List - lorem ipsum aperia</li>\r\n<li>Sign List - lorem ipsum aperia</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="list-3">\r\n<li>Check List lorem ipsum aperia</li>\r\n<li>Check List lorem ipsum aperia</li>\r\n<li>Check List lorem ipsum aperia</li>\r\n<li>Check List lorem ipsum aperia</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="list-4">\r\n<li>Sign List - lorem ipsum aperia</li>\r\n<li>Sign List - lorem ipsum aperia</li>\r\n<li>Sign List - lorem ipsum aperia</li>\r\n<li>Sign List - lorem ipsum aperia</li>\r\n</ul>\r\n</div>\r\n<!-- Container / End -->\r\n<div style="margin: 42px 0;"></div>\r\n<!-- Container -->\r\n<div class="eight columns">\r\n<h3 class="headline">Half Page 1/2</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Praesent ut ante id metus sollicitudin sodales. Mauris dictum, lorem quis arcu fringilla dictum eu eu nisl. Donec rutrum erat non arcu gravida porttitor. Nunc et magna nisi.Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas pretium feugiat, nibh metus ultricies quam, nec venenatis.</p>\r\n</div>\r\n<!-- 1/2 -->\r\n<div class="eight columns">\r\n<h3 class="headline">Half Page 1/2</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Praesent ut ante id metus sollicitudin sodales. Mauris dictum, lorem quis pretium arcu fringilla dictum eu eu nisl. Donec rutrum erat non arcu gravida porttitor. Nunc et magna nisi.Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas feugiat, nibh metus ultricies quam, nec venenati.</p>\r\n</div>\r\n<!-- Container End --> <br /> <!-- Container -->\r\n<div class="one-third column">\r\n<h3 class="headline">One Third 1/3</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Cras semper facilisis vestibulum. Nam purus magna, venenatis a mollis Quisque metus arcu, condimentum vitae dignissim at, condimentum id dui. Fusce at dui metus. Nulla in purus in mauris luctus gravida.</p>\r\n</div>\r\n<!-- 1/3 -->\r\n<div class="one-third column">\r\n<h3 class="headline">One Third 1/3</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Cras semper facilisis vestibulum. Nam purus magna, venenatis a mollis Quisque metus arcu, condimentum vitae dignissim at, condimentum id dui. Fusce at dui metus. Nulla in purus in mauris luctus gravida.</p>\r\n</div>\r\n<!-- 1/3 -->\r\n<div class="one-third column">\r\n<h3 class="headline">One Third 1/3</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Cras semper facilisis vestibulum. Nam purus magna, venenatis a mollis Quisque metus arcu, condimentum vitae dignissim at, condimentum id dui. Fusce at dui metus. Nulla in purus in mauris luctus gravida.</p>\r\n</div>\r\n<div class="one-third column">\r\n<h3 class="headline">One Third 1/3</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla. Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Nullam vitae neque luctus dolor pulvinar condimentum nec sed.</p>\r\n</div>\r\n<!-- 2/3 -->\r\n<div class="two-thirds column">\r\n<h3 class="headline">Two Thirds 2/3</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla. Aliquam arcu fringilla dictum eu eu nisl. Donec rutrum erat non arcu gravida porttitor. Nunc et magna nisi.Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Nullam vitae neque luctus dolor pulvinar condimentum nec sed.</p>\r\n</div>\r\n<!-- Container End --> <br /> <!-- Container -->\r\n<div class="clearfix"></div>\r\n<div class="four columns">\r\n<h3 class="headline">One Fourth 1/4</h3>\r\n<span style="margin-bottom: 25px;" class="line"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla. Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Nullam vitae neque luctus.</p>\r\n</div>\r\n<!-- 1/4 -->\r\n<div class="four columns">\r\n<h3 class="headline">One Fourth 1/4</h3>\r\n<span style="margin-bottom: 25px;" class="line"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla. Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Nullam vitae neque luctus.</p>\r\n</div>\r\n<!-- 1/4 -->\r\n<div class="four columns">\r\n<h3 class="headline">One Fourth 1/4</h3>\r\n<span style="margin-bottom: 25px;" class="line"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla. Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Nullam vitae neque luctus.</p>\r\n</div>\r\n<!-- 1/4 -->\r\n<div class="four columns">\r\n<h3 class="headline">One Fourth 1/4</h3>\r\n<span style="margin-bottom: 30px;" class="line"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla. Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Nullam vitae neque luctus.</p>\r\n</div>\r\n<div class="clearfix"></div>\r\n<div><!-- Container End --> <br /> <!-- Container -->\r\n<div class="twelve columns">\r\n<h3 class="headline">One Fourth 1/4</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Sed at odio ut arcu fringilla dictum eu eu nisl. Donec rutrum erat non arcu gravida porttitor. Nunc et magna nisi.Aliquam at erat in purus aliquet mollis. Fusce elementum velit vel dolor iaculis egestas. Maecenas ut nulla quis eros scelerisque posuere vel vitae nibh. Proin id condimentum sem. Morbi vitae dui in magna vestibulum suscipit vitae vel nunc. Integer ut risus nulla. malesuada tortor, nec scelerisque lorem mattis lore Aliquam at erat in purus aliquet mollis.</p>\r\n</div>\r\n<!-- 3/4 -->\r\n<div class="four columns">\r\n<h3 class="headline">Three Fourth 3/4</h3>\r\n<span class="line" style="margin-bottom: 25px;"></span>\r\n<div class="clearfix"></div>\r\n<p>Magna nisi. Integer ut risus nulla lorem. Aliquam at erat in purus aliquet mollis nullam vitae pulvinar condimentum nec aiquam at erat in purus.</p>\r\n</div>\r\n</div>', 'fullHtml'),
(29, 10, 12, 0, 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. ', '<p>Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.</p>\r\n<p>Огибающая семейства поверхностей,&nbsp;общеизвестно, переворачивает экстремум функции, при этом, вместо 13 можно взять любую другую константу. Дифференциальное исчисление категорически упорядочивает тройной интеграл, таким образом сбылась мечта идиота - утверждение полностью доказано. Интерполяция неоднозначна.</p>\r\n<p>Скалярное произведение позитивно транслирует тригонометрический функциональный анализ. Лемма,&nbsp;общеизвестно, поддерживает расходящийся ряд. Относительная погрешность охватывает экспериментальный критерий интегрируемости. Метод последовательных приближений неоднозначен. Натуральный логарифм развивает эмпирический интеграл по бесконечной области, при этом, вместо 13 можно взять любую другую константу.</p>', 'fullHtml'),
(34, 22, 1, 0, '', '<p><span class="dropcap">К</span>ритерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.</p>\r\n<p>Огибающая семейства поверхностей,&nbsp;общеизвестно, переворачивает экстремум функции, при этом, вместо 13 можно взять любую другую константу. Дифференциальное исчисление категорически упорядочивает тройной интеграл, таким образом сбылась мечта идиота - утверждение полностью доказано. Интерполяция неоднозначна.</p>', 'fullHtml'),
(42, 29, 22, 0, 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'filterHtml'),
(43, 29, 21, 0, 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'filterHtml'),
(45, 29, 24, 0, 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'filterHtml'),
(47, 22, 20, 0, '', '<p><span class="dropcap">П</span>Предсознательное стабильно. Гетерогенность концептуально интегрирует сублимированный эриксоновский гипноз. Сознание противоречиво отчуждает онтогенез речи, и это неудивительно, если речь о персонифицированном характере первичной социализации. Проекция представляет собой ролевой бихевиоризм.</p>\n<p>Установка, согласно традиционным представлениям, отражает социометрический объект. Представленный контент-анализ является психолингвистическим в своей основе, таким образом закон отталкивает экзистенциальный ассоцианизм, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах. Но так как книга Фридмана адресована руководителям и работникам образования, то есть гомеостаз индивидуально отталкивает субъект.</p>\n<p>Стратификация, иcходя из того, что дает депрессивный страх, в частности, "тюремные психозы", индуцируемые при различных психопатологических типологиях. Код вызывает сублимированный код. Коллективное бессознательное, на первый взгляд, кумулятивно.</p>', 'fullHtml'),
(51, 10, 10, 0, 'Огибающая семейства поверхностей, общеизвестно, переворачивает экстремум функции, при этом, вместо 13 можно взять любую другую константу. Дифференциальное исчисление категорически упорядочивает тройной интеграл, таким образом сбылась мечта идиота - утверждение полностью доказано. Интерполяция неоднозначна.', '<p>Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.</p>\r\n<p>Огибающая семейства поверхностей,&nbsp;общеизвестно, переворачивает экстремум функции, при этом, вместо 13 можно взять любую другую константу. Дифференциальное исчисление категорически упорядочивает тройной интеграл, таким образом сбылась мечта идиота - утверждение полностью доказано. Интерполяция неоднозначна.</p>\r\n<p>Скалярное произведение позитивно транслирует тригонометрический функциональный анализ. Лемма,&nbsp;общеизвестно, поддерживает расходящийся ряд. Относительная погрешность охватывает экспериментальный критерий интегрируемости. Метод последовательных приближений неоднозначен. Натуральный логарифм развивает эмпирический интеграл по бесконечной области, при этом, вместо 13 можно взять любую другую константу.</p>', 'fullHtml'),
(52, 10, 11, 0, 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', '<p>Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.</p>\r\n<p>Огибающая семейства поверхностей,&nbsp;общеизвестно, переворачивает экстремум функции, при этом, вместо 13 можно взять любую другую константу. Дифференциальное исчисление категорически упорядочивает тройной интеграл, таким образом сбылась мечта идиота - утверждение полностью доказано. Интерполяция неоднозначна.</p>\r\n<p>Скалярное произведение позитивно транслирует тригонометрический функциональный анализ. Лемма,&nbsp;общеизвестно, поддерживает расходящийся ряд. Относительная погрешность охватывает экспериментальный критерий интегрируемости. Метод последовательных приближений неоднозначен. Натуральный логарифм развивает эмпирический интеграл по бесконечной области, при этом, вместо 13 можно взять любую другую константу.</p>', 'filterHtml'),
(57, 16, 3, 0, 'Дифференциальное уравнение монотонно. Не доказано, что полином иррационален. Можно предположить, что многочлен изящно развивает детерминант.Продолжая до бесконечности ряд 1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31 и т.д., имеем абсолютно сходящийся ряд позитивно отображает параллельный интеграл по поверхности. Теорема детерменирована. Иррациональное число недоказуемо. Используя таблицу интегралов элементарных функций, получим: линейное ', '<p>Дифференциальное уравнение монотонно. Не доказано, что полином иррационален. Можно&nbsp;предположить,&nbsp;что многочлен изящно развивает детерминант.</p>\r\n<blockquote>\r\n<p>Продолжая до бесконечности ряд 1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31 и т.д., имеем абсолютно сходящийся ряд позитивно отображает параллельный интеграл по поверхности. Теорема детерменирована. Иррациональное число недоказуемо. Используя таблицу интегралов элементарных функций, получим: линейное программирование существенно раскручивает интеграл от функции, имеющий конечный разрыв. Замкнутое множество, исключая очевидный случай, синхронизирует минимум. Нечетная функция,&nbsp;следовательно, изящно развивает интеграл по ориентированной области.</p>\r\n</blockquote>\r\n<p>Целое число переворачивает предел последовательности. Контрпример непосредственно восстанавливает определитель системы линейных уравнений, что неудивительно. Отсюда&nbsp;естественно&nbsp;следует,&nbsp;что постоянная величина притягивает определитель системы линейных уравнений.</p>', 'fullHtml'),
(58, 16, 28, 0, 'Матожидание программирует равновероятный абсолютно сходящийся ряд. Интеграл от функции, обращающейся в бесконечность в изолированной точке, очевидно, соответствует интеграл Дирихле. Дифференциальное исчисление, в первом приближении, порождает интеграл от функции, имеющий конечный разрыв. Относительная погрешность, очевидно, последовательно уравновешивает стремящийся тройной интеграл, что несомненно приведет нас к истине. Нормаль к поверхности очевидна не для всех.', '<p>Матожидание программирует равновероятный абсолютно сходящийся ряд. Интеграл от функции, обращающейся в бесконечность в изолированной точке,&nbsp;очевидно, соответствует интеграл Дирихле. Дифференциальное исчисление, в первом приближении, порождает интеграл от функции, имеющий конечный разрыв. Относительная погрешность,&nbsp;очевидно, последовательно уравновешивает стремящийся тройной интеграл, что несомненно приведет нас к истине. Нормаль к поверхности очевидна&nbsp;не&nbsp;для&nbsp;всех.</p>\r\n<p>Подынтегральное выражение непредсказуемо. Криволинейный интеграл,&nbsp;очевидно, естественно охватывает положительный скачок функции. Точка перегиба,&nbsp;очевидно, положительна. Сходящийся ряд масштабирует постулат. Интеграл Дирихле нормально распределен.</p>\r\n<p>Линейное уравнение, в первом приближении, продуцирует параллельный интеграл от функции, имеющий конечный разрыв. Рассмотрим непрерывную функцию y = f ( x ), заданную на отрезке [ a, b ], рациональное число однородно охватывает степенной ряд. Несмотря на сложности, бесконечно малая величина искажает ротор векторного поля. Нормальное распределение нетривиально. Линейное программирование проецирует аксиоматичный контрпример.</p>', 'fullHtml'),
(59, 16, 29, 0, 'Поле направлений, в первом приближении, охватывает абстрактный вектор. Векторное поле, не вдаваясь в подробности, допускает полином. Криволинейный интеграл позитивно трансформирует нормальный интеграл Пуассона. Сходящийся ряд традиционно раскручивает невероятный натуральный логарифм. Наряду с этим, функция выпуклая книзу по-прежнему востребована. Математическая статистика вырождена.', '<p>Минимум правомочен. Лист Мёбиуса трансформирует критерий сходимости Коши, дальнейшие выкладки оставим студентам в качестве несложной домашней работы. Связное множество, не вдаваясь в подробности, отображает положительный тройной интеграл. Рассмотрим непрерывную функцию y = f ( x ), заданную на отрезке [ a, b ], число е порождает изоморфный разрыв функции.</p>\r\n<p>Поле направлений, в первом приближении, охватывает абстрактный вектор. Векторное поле, не вдаваясь в подробности, допускает полином. Криволинейный интеграл позитивно трансформирует нормальный интеграл Пуассона. Сходящийся ряд традиционно раскручивает невероятный натуральный логарифм. Наряду с этим, функция выпуклая книзу по-прежнему востребована. Математическая статистика вырождена.</p>\r\n<p>Лист Мёбиуса стремительно допускает стремящийся экстремум функции, откуда следует доказываемое равенство. Представляется&nbsp;логичным,&nbsp;что наибольшее и наименьшее значения функции уравновешивает сходящийся ряд. Умножение двух векторов (векторное) необходимо и достаточно. Длина вектора накладывает изоморфный ряд Тейлора, явно демонстрируя всю чушь вышесказанного. Интегрирование по частям, исключая очевидный случай, последовательно упорядочивает неопровержимый постулат.</p>', 'fullHtml'),
(60, 16, 30, 0, 'Скачок функции, конечно, переворачивает интеграл Фурье. Можно предположить, что линейное программирование привлекает интеграл по ориентированной области. Огибающая семейства поверхностей отображает интеграл Фурье. Число е решительно допускает разрыв функции. Тем не менее, поле направлений восстанавливает определитель системы линейных уравнений.', '<p>Скачок функции,&nbsp;конечно, переворачивает интеграл Фурье. Можно&nbsp;предположить,&nbsp;что линейное программирование привлекает интеграл по ориентированной области. Огибающая семейства поверхностей отображает интеграл Фурье. Число е решительно допускает разрыв функции. Тем не менее, поле направлений восстанавливает определитель системы линейных уравнений.</p>\r\n<p>Линейное уравнение накладывает расходящийся ряд. Матожидание однородно накладывает интеграл по поверхности. Функция B(x,y) упорядочивает предел функции, в итоге приходим к логическому противоречию. Интеграл от функции комплексной переменной,&nbsp;следовательно, усиливает анормальный максимум.</p>\r\n<p>Высшая арифметика транслирует скачок функции. Стоит отметить, что линейное уравнение последовательно. Линейное программирование,&nbsp;очевидно, позитивно упорядочивает интеграл Гамильтона.</p>', 'fullHtml'),
(62, 16, 2, 0, 'Интеграл Гамильтона соответствует скачок функции. Интеграл Дирихле, очевидно, отнюдь не очевиден. Согласно предыдущему, матожидание поддерживает полином. Непрерывная функция последовательно порождает интеграл Гамильтона, как и предполагалось. Следствие: теорема Гаусса - Остроградского существенно охватывает скачок функции, дальнейшие выкладки оставим студентам в качестве несложной домашней работы. Огибающая семейства поверхностей, в первом приближении, отображает критерий интегрируемости, как и предполагалось.', '<p>Интеграл Гамильтона соответствует скачок функции. Интеграл Дирихле,&nbsp;очевидно, отнюдь не&nbsp;очевиден. Согласно&nbsp;предыдущему, матожидание поддерживает полином. Непрерывная функция последовательно порождает интеграл Гамильтона, как и предполагалось. Следствие: теорема Гаусса - Остроградского существенно охватывает скачок функции, дальнейшие выкладки оставим студентам в качестве несложной домашней работы. Огибающая семейства поверхностей, в первом приближении, отображает критерий интегрируемости, как и предполагалось.</p>\r\n<blockquote>\r\n<p>Уравнение в частных производных реально концентрирует бином Ньютона, что и требовалось доказать. Интеграл от функции, имеющий конечный разрыв небезынтересно уравновешивает минимум. Тем не менее, векторное поле создает максимум. Не доказано, что интерполяция в принципе соответствует экстремум функции. Открытое множество концентрирует коллинеарный функциональный анализ. Связное множество,&nbsp;конечно, необходимо и достаточно.</p>\r\n</blockquote>\r\n<p>Можно&nbsp;предположить,&nbsp;что Наибольший Общий Делитель (НОД) категорически продуцирует интеграл Гамильтона. Минимум продуцирует интеграл Дирихле, что неудивительно. Согласно&nbsp;предыдущему, подмножество обуславливает анормальный определитель системы линейных уравнений.</p>', 'fullHtml'),
(66, 22, 31, 0, '', '<p>Форма политического сознания очевидна не для всех. Политическое учение Руссо вызывает экзистенциальный континентально-европейский тип политической культуры. Политические учения Гоббса косвенно. Информационно-технологическая революция иллюстрирует континентально-европейский тип политической культуры. Политическая психология, в первом приближении, обретает конструктивный тоталитарный тип политической культуры. Конечно, харизматическое лидерство интегрирует тоталитарный тип политической культуры, говорится в докладе ОБСЕ</p>\r\n<br /><br /><br />', 'fullHtml'),
(67, 29, 23, 0, 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд. Более того, нормаль к поверхности транслирует невероятный критерий сходимости Коши. Бесконечно малая величина проецирует параллельный абсолютно сходящийся ряд, явно демонстрируя всю чушь вышесказанного. Степенной ряд естественно изменяет отрицательный график функции многих переменных.', 'filterHtml'),
(68, 22, 16, 0, '', '<div class="container">\r\n<div class="sixteen columns">\r\n<div class="notification notice closeable" style="margin: 0 0 30px 0;">\r\n<p>Шаблон включает набор более чем 300 популярный иконок высокой четкости</p>\r\n<a class="close" href="#"></a></div>\r\n</div>\r\n<div class="sixteen columns">\r\n<h3 class="headline">Web Application Icons</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-adjust"></i> icon-adjust</li>\r\n<li><i class="icon-anchor"></i> icon-anchor</li>\r\n<li><i class="icon-asterisk"></i> icon-asterisk</li>\r\n<li><i class="icon-ban-circle"></i> icon-ban-circle</li>\r\n<li><i class="icon-bar-chart"></i> icon-bar-chart</li>\r\n<li><i class="icon-barcode"></i> icon-barcode</li>\r\n<li><i class="icon-beaker"></i> icon-beaker</li>\r\n<li><i class="icon-beer"></i> icon-beer</li>\r\n<li><i class="icon-bell-alt"></i> icon-bell-alt</li>\r\n<li><i class="icon-bell"></i> icon-bell</li>\r\n<li><i class="icon-bolt"></i> icon-bolt</li>\r\n<li><i class="icon-book"></i> icon-book</li>\r\n<li><i class="icon-bookmark-empty"></i> icon-bookmark-empty</li>\r\n<li><i class="icon-bookmark"></i> icon-bookmark</li>\r\n<li><i class="icon-briefcase"></i> icon-briefcase</li>\r\n<li><i class="icon-bullhorn"></i> icon-bullhorn</li>\r\n<li><i class="icon-bullseye"></i> icon-bullseye</li>\r\n<li><i class="icon-calendar-empty"></i> icon-calendar-empty</li>\r\n<li><i class="icon-calendar"></i> icon-calendar</li>\r\n<li><i class="icon-camera-retro"></i> icon-camera-retro</li>\r\n<li><i class="icon-camera"></i> icon-camera</li>\r\n<li><i class="icon-certificate"></i> icon-certificate</li>\r\n<li><i class="icon-check-empty"></i> icon-check-empty</li>\r\n<li><i class="icon-check-minus"></i> icon-check-minus</li>\r\n<li><i class="icon-check-sign"></i> icon-check-sign</li>\r\n<li><i class="icon-check"></i> icon-check</li>\r\n<li><i class="icon-circle-blank"></i> icon-circle-blank</li>\r\n<li><i class="icon-circle"></i> icon-circle</li>\r\n<li><i class="icon-cloud-download"></i> icon-cloud-download</li>\r\n<li><i class="icon-cloud-upload"></i> icon-cloud-upload</li>\r\n<li><i class="icon-cloud"></i> icon-cloud</li>\r\n<li><i class="icon-code-fork"></i> icon-code-fork</li>\r\n<li><i class="icon-code"></i> icon-code</li>\r\n<li><i class="icon-coffee"></i> icon-coffee</li>\r\n<li><i class="icon-cog"></i> icon-cog</li>\r\n<li><i class="icon-cogs"></i> icon-cogs</li>\r\n<li><i class="icon-collapse-alt"></i> icon-collapse-alt</li>\r\n<li><i class="icon-comment-alt"></i> icon-comment-alt</li>\r\n<li><i class="icon-comment"></i> icon-comment</li>\r\n<li><i class="icon-comments-alt"></i> icon-comments-alt</li>\r\n<li><i class="icon-comments"></i> icon-comments</li>\r\n<li><i class="icon-credit-card"></i> icon-credit-card</li>\r\n<li><i class="icon-crop"></i> icon-crop</li>\r\n<li><i class="icon-dashboard"></i> icon-dashboard</li>\r\n<li><i class="icon-desktop"></i> icon-desktop</li>\r\n<li><i class="icon-download-alt"></i> icon-download-alt</li>\r\n<li><i class="icon-download"></i> icon-download</li>\r\n<li><i class="icon-edit-sign"></i> icon-edit-sign</li>\r\n<li><i class="icon-edit"></i> icon-edit</li>\r\n<li><i class="icon-ellipsis-horizontal"></i> icon-ellipsis-horizontal</li>\r\n<li><i class="icon-ellipsis-vertical"></i> icon-ellipsis-vertical</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-envelope-alt"></i> icon-envelope-alt</li>\r\n<li><i class="icon-envelope"></i> icon-envelope</li>\r\n<li><i class="icon-eraser"></i> icon-eraser</li>\r\n<li><i class="icon-exchange"></i> icon-exchange</li>\r\n<li><i class="icon-exclamation-sign"></i> icon-exclamation-sign</li>\r\n<li><i class="icon-exclamation"></i> icon-exclamation</li>\r\n<li><i class="icon-expand-alt"></i> icon-expand-alt</li>\r\n<li><i class="icon-external-link-sign"></i> icon-external-link-sign</li>\r\n<li><i class="icon-external-link"></i> icon-external-link</li>\r\n<li><i class="icon-eye-close"></i> icon-eye-close</li>\r\n<li><i class="icon-eye-open"></i> icon-eye-open</li>\r\n<li><i class="icon-facetime-video"></i> icon-facetime-video</li>\r\n<li><i class="icon-fighter-jet"></i> icon-fighter-jet</li>\r\n<li><i class="icon-film"></i> icon-film</li>\r\n<li><i class="icon-filter"></i> icon-filter</li>\r\n<li><i class="icon-fire-extinguisher"></i> icon-fire-extinguisher</li>\r\n<li><i class="icon-fire"></i> icon-fire</li>\r\n<li><i class="icon-flag-alt"></i> icon-flag-alt</li>\r\n<li><i class="icon-flag-checkered"></i> icon-flag-checkered</li>\r\n<li><i class="icon-flag"></i> icon-flag</li>\r\n<li><i class="icon-folder-close-alt"></i> icon-folder-close-alt</li>\r\n<li><i class="icon-folder-close"></i> icon-folder-close</li>\r\n<li><i class="icon-folder-open-alt"></i> icon-folder-open-alt</li>\r\n<li><i class="icon-folder-open"></i> icon-folder-open</li>\r\n<li><i class="icon-food"></i> icon-food</li>\r\n<li><i class="icon-frown"></i> icon-frown</li>\r\n<li><i class="icon-gamepad"></i> icon-gamepad</li>\r\n<li><i class="icon-gift"></i> icon-gift</li>\r\n<li><i class="icon-glass"></i> icon-glass</li>\r\n<li><i class="icon-globe"></i> icon-globe</li>\r\n<li><i class="icon-group"></i> icon-group</li>\r\n<li><i class="icon-hdd"></i> icon-hdd</li>\r\n<li><i class="icon-headphones"></i> icon-headphones</li>\r\n<li><i class="icon-heart-empty"></i> icon-heart-empty</li>\r\n<li><i class="icon-heart"></i> icon-heart</li>\r\n<li><i class="icon-home"></i> icon-home</li>\r\n<li><i class="icon-inbox"></i> icon-inbox</li>\r\n<li><i class="icon-info-sign"></i> icon-info-sign</li>\r\n<li><i class="icon-info"></i> icon-info</li>\r\n<li><i class="icon-key"></i> icon-key</li>\r\n<li><i class="icon-keyboard"></i> icon-keyboard</li>\r\n<li><i class="icon-laptop"></i> icon-laptop</li>\r\n<li><i class="icon-leaf"></i> icon-leaf</li>\r\n<li><i class="icon-legal"></i> icon-legal</li>\r\n<li><i class="icon-lemon"></i> icon-lemon</li>\r\n<li><i class="icon-level-down"></i> icon-level-down</li>\r\n<li><i class="icon-level-up"></i> icon-level-up</li>\r\n<li><i class="icon-lightbulb"></i> icon-lightbulb</li>\r\n<li><i class="icon-location-arrow"></i> icon-location-arrow</li>\r\n<li><i class="icon-lock"></i> icon-lock</li>\r\n<li><i class="icon-magic"></i> icon-magic</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-magnet"></i> icon-magnet</li>\r\n<li><i class="icon-mail-forward"></i> icon-mail-forward</li>\r\n<li><i class="icon-mail-reply"></i> icon-mail-reply</li>\r\n<li><i class="icon-mail-reply-all"></i> icon-mail-reply-all</li>\r\n<li><i class="icon-map-marker"></i> icon-map-marker</li>\r\n<li><i class="icon-meh"></i> icon-meh</li>\r\n<li><i class="icon-microphone-off"></i> icon-microphone-off</li>\r\n<li><i class="icon-microphone"></i> icon-microphone</li>\r\n<li><i class="icon-minus-sign-alt"></i> icon-minus-sign-alt</li>\r\n<li><i class="icon-minus-sign"></i> icon-minus-sign</li>\r\n<li><i class="icon-minus"></i> icon-minus</li>\r\n<li><i class="icon-mobile-phone"></i> icon-mobile-phone</li>\r\n<li><i class="icon-money"></i> icon-money</li>\r\n<li><i class="icon-move"></i> icon-move</li>\r\n<li><i class="icon-music"></i> icon-music</li>\r\n<li><i class="icon-off"></i> icon-off</li>\r\n<li><i class="icon-ok-circle"></i> icon-ok-circle</li>\r\n<li><i class="icon-ok-sign"></i> icon-ok-sign</li>\r\n<li><i class="icon-ok"></i> icon-ok</li>\r\n<li><i class="icon-pencil"></i> icon-pencil</li>\r\n<li><i class="icon-phone-sign"></i> icon-phone-sign</li>\r\n<li><i class="icon-phone"></i> icon-phone</li>\r\n<li><i class="icon-picture"></i> icon-picture</li>\r\n<li><i class="icon-plane"></i> icon-plane</li>\r\n<li><i class="icon-plus-sign"></i> icon-plus-sign</li>\r\n<li><i class="icon-plus"></i> icon-plus</li>\r\n<li><i class="icon-print"></i> icon-print</li>\r\n<li><i class="icon-pushpin"></i> icon-pushpin</li>\r\n<li><i class="icon-puzzle-piece"></i> icon-puzzle-piece</li>\r\n<li><i class="icon-qrcode"></i> icon-qrcode</li>\r\n<li><i class="icon-question-sign"></i> icon-question-sign</li>\r\n<li><i class="icon-question"></i> icon-question</li>\r\n<li><i class="icon-quote-left"></i> icon-quote-left</li>\r\n<li><i class="icon-quote-right"></i> icon-quote-right</li>\r\n<li><i class="icon-random"></i> icon-random</li>\r\n<li><i class="icon-refresh"></i> icon-refresh</li>\r\n<li><i class="icon-remove-circle"></i> icon-remove-circle</li>\r\n<li><i class="icon-remove-sign"></i> icon-remove-sign</li>\r\n<li><i class="icon-remove"></i> icon-remove</li>\r\n<li><i class="icon-reorder"></i> icon-reorder</li>\r\n<li><i class="icon-reply-all"></i> icon-reply-all</li>\r\n<li><i class="icon-reply"></i> icon-reply</li>\r\n<li><i class="icon-resize-horizontal"></i> icon-resize-horizontal</li>\r\n<li><i class="icon-resize-vertical"></i> icon-resize-vertical</li>\r\n<li><i class="icon-retweet"></i> icon-retweet</li>\r\n<li><i class="icon-road"></i> icon-road</li>\r\n<li><i class="icon-rocket"></i> icon-rocket</li>\r\n<li><i class="icon-rotate-left"></i> icon-rotate-left</li>\r\n<li><i class="icon-rotate-right"></i> icon-rotate-right</li>\r\n<li><i class="icon-rss-sign"></i> icon-rss-sign</li>\r\n<li><i class="icon-css3"></i> icon-css3</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-screenshot"></i> icon-screenshot</li>\r\n<li><i class="icon-search"></i> icon-search</li>\r\n<li><i class="icon-share-alt"></i> icon-share-alt</li>\r\n<li><i class="icon-share-sign"></i> icon-share-sign</li>\r\n<li><i class="icon-share"></i> icon-share</li>\r\n<li><i class="icon-shield"></i> icon-shield</li>\r\n<li><i class="icon-shopping-cart"></i> icon-shopping-cart</li>\r\n<li><i class="icon-sign-blank"></i> icon-sign-blank</li>\r\n<li><i class="icon-signal"></i> icon-signal</li>\r\n<li><i class="icon-signin"></i> icon-signin</li>\r\n<li><i class="icon-signout"></i> icon-signout</li>\r\n<li><i class="icon-sitemap"></i> icon-sitemap</li>\r\n<li><i class="icon-smile"></i> icon-smile</li>\r\n<li><i class="icon-sort-down"></i> icon-sort-down</li>\r\n<li><i class="icon-sort-up"></i> icon-sort-up</li>\r\n<li><i class="icon-sort"></i> icon-sort</li>\r\n<li><i class="icon-spinner"></i> icon-spinner</li>\r\n<li><i class="icon-star-empty"></i> icon-star-empty</li>\r\n<li><i class="icon-star-half-full"></i> icon-star-half-full</li>\r\n<li><i class="icon-star-half-empty"></i> icon-star-half-empty</li>\r\n<li><i class="icon-star-half"></i> icon-star-half</li>\r\n<li><i class="icon-star"></i> icon-star</li>\r\n<li><i class="icon-tablet"></i> icon-tablet</li>\r\n<li><i class="icon-tag"></i> icon-tag</li>\r\n<li><i class="icon-tags"></i> icon-tags</li>\r\n<li><i class="icon-tasks"></i> icon-tasks</li>\r\n<li><i class="icon-terminal"></i> icon-terminal</li>\r\n<li><i class="icon-thumbs-down"></i> icon-thumbs-down</li>\r\n<li><i class="icon-thumbs-up"></i> icon-thumbs-up</li>\r\n<li><i class="icon-ticket"></i> icon-ticket</li>\r\n<li><i class="icon-time"></i> icon-time</li>\r\n<li><i class="icon-tint"></i> icon-tint</li>\r\n<li><i class="icon-trash"></i> icon-trash</li>\r\n<li><i class="icon-trophy"></i> icon-trophy</li>\r\n<li><i class="icon-truck"></i> icon-truck</li>\r\n<li><i class="icon-umbrella"></i> icon-umbrella</li>\r\n<li><i class="icon-unlock-alt"></i> icon-unlock-alt</li>\r\n<li><i class="icon-unlock"></i> icon-unlock</li>\r\n<li><i class="icon-upload-alt"></i> icon-upload-alt</li>\r\n<li><i class="icon-upload"></i> icon-upload</li>\r\n<li><i class="icon-user-md"></i> icon-user-md</li>\r\n<li><i class="icon-user"></i> icon-user</li>\r\n<li><i class="icon-volume-down"></i> icon-volume-down</li>\r\n<li><i class="icon-volume-off"></i> icon-volume-off</li>\r\n<li><i class="icon-volume-up"></i> icon-volume-up</li>\r\n<li><i class="icon-warning-sign"></i> icon-warning-sign</li>\r\n<li><i class="icon-wrench"></i> icon-wrench</li>\r\n<li><i class="icon-zoom-in"></i> icon-zoom-in</li>\r\n<li><i class="icon-zoom-out"></i> icon-zoom-out</li>\r\n<li><i class="icon-html5"></i> icon-html5</li>\r\n</ul>\r\n</div>\r\n</div>\r\n<!-- Container / End -->\r\n<div style="margin: 33px 0;"></div>\r\n<!-- Container -->\r\n<div class="container">\r\n<div class="sixteen columns">\r\n<h3 class="headline">Text Editor Icons</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-file"></i> icon-file</li>\r\n<li><i class="icon-file-alt"></i> icon-file-alt</li>\r\n<li><i class="icon-cut"></i> icon-cut</li>\r\n<li><i class="icon-copy"></i> icon-copy</li>\r\n<li><i class="icon-paste"></i> icon-paste</li>\r\n<li><i class="icon-save"></i> icon-save</li>\r\n<li><i class="icon-undo"></i> icon-undo</li>\r\n<li><i class="icon-repeat"></i> icon-repeat</li>\r\n<li><i class="icon-text-height"></i> icon-text-height</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-text-width"></i> icon-text-width</li>\r\n<li><i class="icon-align-left"></i> icon-align-left</li>\r\n<li><i class="icon-align-center"></i> icon-align-center</li>\r\n<li><i class="icon-align-right"></i> icon-align-right</li>\r\n<li><i class="icon-align-justify"></i> icon-align-justify</li>\r\n<li><i class="icon-indent-left"></i> icon-indent-left</li>\r\n<li><i class="icon-indent-right"></i> icon-indent-right</li>\r\n<li><i class="icon-font"></i> icon-font</li>\r\n<li><i class="icon-bold"></i> icon-bold</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-italic"></i> icon-italic</li>\r\n<li><i class="icon-strikethrough"></i> icon-strikethrough</li>\r\n<li><i class="icon-underline"></i> icon-underline</li>\r\n<li><i class="icon-superscript"></i> icon-superscript</li>\r\n<li><i class="icon-subscript"></i> icon-subscript</li>\r\n<li><i class="icon-link"></i> icon-link</li>\r\n<li><i class="icon-unlink"></i> icon-unlink</li>\r\n<li><i class="icon-paper-clip"></i> icon-paper-clip</li>\r\n<li><i class="icon-eraser"></i> icon-eraser</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-columns"></i> icon-columns</li>\r\n<li><i class="icon-table"></i> icon-table</li>\r\n<li><i class="icon-th-large"></i> icon-th-large</li>\r\n<li><i class="icon-th"></i> icon-th</li>\r\n<li><i class="icon-th-list"></i> icon-th-list</li>\r\n<li><i class="icon-list"></i> icon-list</li>\r\n<li><i class="icon-list-ol"></i> icon-list-ol</li>\r\n<li><i class="icon-list-ul"></i> icon-list-ul</li>\r\n<li><i class="icon-list-alt"></i> icon-list-alt</li>\r\n</ul>\r\n</div>\r\n</div>\r\n<!-- Container / End -->\r\n<div style="margin: 33px 0;"></div>\r\n<!-- Container -->\r\n<div class="container">\r\n<div class="sixteen columns">\r\n<h3 class="headline">Directional Icons</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-angle-left"></i> icon-angle-left</li>\r\n<li><i class="icon-angle-right"></i> icon-angle-right</li>\r\n<li><i class="icon-angle-up"></i> icon-angle-up</li>\r\n<li><i class="icon-angle-down"></i> icon-angle-down</li>\r\n<li><i class="icon-arrow-down"></i> icon-arrow-down</li>\r\n<li><i class="icon-arrow-left"></i> icon-arrow-left</li>\r\n<li><i class="icon-arrow-right"></i> icon-arrow-right</li>\r\n<li><i class="icon-arrow-up"></i> icon-arrow-up</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-caret-down"></i> icon-caret-down</li>\r\n<li><i class="icon-caret-left"></i> icon-caret-left</li>\r\n<li><i class="icon-caret-right"></i> icon-caret-right</li>\r\n<li><i class="icon-caret-up"></i> icon-caret-up</li>\r\n<li><i class="icon-chevron-down"></i> icon-chevron-down</li>\r\n<li><i class="icon-chevron-left"></i> icon-chevron-left</li>\r\n<li><i class="icon-chevron-right"></i> icon-chevron-right</li>\r\n<li><i class="icon-chevron-up"></i> icon-chevron-up</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-chevron-sign-left"></i> icon-chevron-sign-left</li>\r\n<li><i class="icon-chevron-sign-right"></i> icon-chevron-sign-right</li>\r\n<li><i class="icon-chevron-sign-up"></i> icon-chevron-sign-up</li>\r\n<li><i class="icon-chevron-sign-down"></i> icon-chevron-sign-down</li>\r\n<li><i class="icon-circle-arrow-down"></i> icon-circle-arrow-down</li>\r\n<li><i class="icon-circle-arrow-left"></i> icon-circle-arrow-left</li>\r\n<li><i class="icon-circle-arrow-right"></i> icon-circle-arrow-right</li>\r\n<li><i class="icon-circle-arrow-up"></i> icon-circle-arrow-up</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-double-angle-left"></i> icon-double-angle-left</li>\r\n<li><i class="icon-double-angle-right"></i> icon-double-angle-right</li>\r\n<li><i class="icon-double-angle-up"></i> icon-double-angle-up</li>\r\n<li><i class="icon-double-angle-down"></i> icon-double-angle-down</li>\r\n<li><i class="icon-hand-down"></i> icon-hand-down</li>\r\n<li><i class="icon-hand-left"></i> icon-hand-left</li>\r\n<li><i class="icon-hand-right"></i> icon-hand-right</li>\r\n<li><i class="icon-hand-up"></i> icon-hand-up</li>\r\n</ul>\r\n</div>\r\n</div>\r\n<!-- Container / End -->\r\n<div style="margin: 33px 0;"></div>\r\n<!-- Container -->\r\n<div class="container">\r\n<div class="sixteen columns">\r\n<h3 class="headline">Video Player Icons</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-play-circle"></i> icon-play-circle</li>\r\n<li><i class="icon-play-sign"></i> icon-play-sign</li>\r\n<li><i class="icon-play"></i> icon-play</li>\r\n<li><i class="icon-pause"></i> icon-pause</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-stop"></i> icon-stop</li>\r\n<li><i class="icon-eject"></i> icon-eject</li>\r\n<li><i class="icon-backward"></i> icon-backward</li>\r\n<li><i class="icon-forward"></i> icon-forward</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-fast-backward"></i> icon-fast-backward</li>\r\n<li><i class="icon-fast-forward"></i> icon-fast-forward</li>\r\n<li><i class="icon-step-backward"></i> icon-step-backward</li>\r\n<li><i class="icon-step-forward"></i> icon-step-forward</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-fullscreen"></i> icon-fullscreen</li>\r\n<li><i class="icon-resize-full"></i> icon-resize-full</li>\r\n<li><i class="icon-resize-small"></i> icon-resize-small</li>\r\n</ul>\r\n</div>\r\n</div>\r\n<!-- Container / End -->\r\n<div style="margin: 33px 0;"></div>\r\n<!-- Container -->\r\n<div class="container">\r\n<div class="sixteen columns">\r\n<h3 class="headline">Medical Icons</h3>\r\n<span class="line" style="margin-bottom: 30px;"></span>\r\n<div class="clearfix"></div>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-ambulance"></i> icon-ambulance</li>\r\n<li><i class="icon-beaker"></i> icon-beaker</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-h-sign"></i> icon-h-sign</li>\r\n<li><i class="icon-hospital"></i> icon-hospital</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-medkit"></i> icon-medkit</li>\r\n<li><i class="icon-plus-sign-alt"></i> icon-plus-sign-alt</li>\r\n</ul>\r\n</div>\r\n<div class="four columns">\r\n<ul class="the-icons">\r\n<li><i class="icon-stethoscope"></i> icon-stethoscope</li>\r\n<li><i class="icon-user-md"></i> icon-user-md</li>\r\n</ul>\r\n</div>\r\n</div>\r\n<!-- Container / End -->', 'fullHtml');

INSERT INTO {{%field_body_body}} (`id`, `field_exp_id`, `entry_id`, `id_lang`, `text_prev`, `text_full`, `text_format`) VALUES
(73, 4, 4, 0, '', '<div class="caption text sfb" data-x="0" data-y="170" data-speed="400" data-start="800" data-easing="easeOutExpo">\r\n<h2>Думай творчески</h2>\r\n</div>\r\n<div class="caption text sfb" data-x="1" data-y="210" data-speed="400" data-start="1000" data-easing="easeOutExpo">\r\n<h3>Позвольте творчеситву развиваться</h3>\r\n</div>\r\n<div class="caption text sfb" data-x="1" data-y="266" data-speed="400" data-start="1200" data-easing="easeOutExpo">\r\n<p>Наш великолепный веб-дизайн позволит вам <br /> передать ваши мысли всему online сообществу</p>\r\n</div>\r\n<div class="caption sft" data-x="450" data-y="140" data-speed="600" data-start="1300" data-easing="easeOutExpo"><img src="/images/shared/slider-07.png" alt="" /></div>\r\n<div class="caption sfb" data-x="570" data-y="215" data-speed="600" data-start="1300" data-easing="easeOutExpo"><img src="/images/shared/slider-08.png" alt="" /></div>\r\n<div class="caption sfb" data-x="600" data-y="31" data-speed="600" data-start="1200" data-easing="easeOutExpo"><img src="/images/shared/slider-01.png" alt="" /></div>', 'fullHtml'),
(77, 4, 5, 0, '', '<div class="caption light text sfb" data-x="0" data-y="170" data-speed="400" data-start="800" data-easing="easeOutExpo">\r\n<h2>Обучающее видео</h2>\r\n</div>\r\n<div class="caption light text sfb" data-x="1" data-y="210" data-speed="400" data-start="1000" data-easing="easeOutExpo">\r\n<h3>Поможет быстро освоить <br /> работу с сайтом</h3>\r\n</div>\r\n<div class="caption light text sfb" data-x="1" data-y="286" data-speed="400" data-start="1200" data-easing="easeOutExpo">\r\n<p>Конструктор контента (Content Construction Kit) MAZE-CMS<br /> спсобен расширяться до бесконечности</p>\r\n</div>\r\n<div class="caption sfb" data-x="440" data-y="60" data-speed="600" data-start="1100" data-easing="easeOutExpo"><img src="/images/shared/slider-10.png" alt="" /></div>\r\n<div class="caption sfb video" data-autoplay="false" data-x="622" data-y="96" data-speed="500" data-start="1100" data-easing="easeOutExpo"></div>', 'fullHtml'),
(78, 4, 6, 0, '', '<div class="caption text sfb" data-x="0" data-y="170" data-speed="400" data-start="800" data-easing="easeOutExpo">\r\n<h2>Сверх четкий</h2>\r\n</div>\r\n<div class="caption text sfb" data-x="1" data-y="210" data-speed="400" data-start="1000" data-easing="easeOutExpo">\r\n<h3>Полностью адаптивный</h3>\r\n</div>\r\n<div class="caption text sfb" data-x="1" data-y="266" data-speed="400" data-start="1200" data-easing="easeOutExpo">\r\n<p>Наш веб-дизайн поддерживат все устройсва<br /> в том числе и на дисплеях высокого разрешения</p>\r\n</div>\r\n<div class="caption sfb" data-x="520" data-y="114" data-speed="600" data-start="1100" data-easing="easeOutExpo"><img src="/images/shared/slider-04.png" alt="" /></div>\r\n<div class="caption sfl" data-x="385" data-y="177" data-speed="600" data-start="1100" data-easing="easeOutExpo"><img src="/images/shared/slider-05.png" alt="" /></div>\r\n<div class="caption sfr" data-x="1005" data-y="220" data-speed="600" data-start="1100" data-easing="easeOutExpo"><img src="/images/shared/slider-06.png" alt="" /></div>', 'fullHtml');

--
-- Конструктор блоков
--

INSERT INTO {{%constructorblock_block}} (`code`, `bundle`, `expansion`, `title`, `description`, `list`, `multiple_size`, `multiple_start`, `date_create`) VALUES
('categoryportfolio', 'portfoliocat', 'dictionary', 'Категории порфолио', 'Выводит категории портфолио', 1, 10, NULL, '2015-10-10 21:23:37'),
('clients', 'clients', 'contents', 'Наши клинеты', 'Выводит тип материалов "Наши клиенты"', 1, 20, NULL, '2015-10-07 21:04:42'),
('featured', 'featured', 'contents', 'Наши преимущества', 'Выводит тип материала "Преимущества"', 1, 12, NULL, '2015-10-07 01:45:39'),
('feedback', 'portfolio', 'contents', 'Отзывы', 'Отзывы о проделанной работе, выводятся из портфолио', 1, 20, NULL, '2015-10-09 22:50:01'),
('homeslider', 'homeslider', 'contents', 'Слайдер главной', 'выводит материалы типа "Слайдер главной"', 1, 10, NULL, '2015-10-07 01:05:23'),
('portfolio', 'portfolio', 'contents', 'Портфолио', 'выводит материалы типа "Портфолио"', 1, 20, NULL, '2015-10-07 20:07:55'),
('services', 'services', 'contents', 'Наши услуги', 'Выводит тип материала наши услуги (анонс)', 1, 4, NULL, '2015-10-10 09:30:16'),
('staff', 'employees', 'contents', 'Сотрудники', 'Сотрудники вашей компании', 1, 20, NULL, '2015-10-09 22:06:36');

INSERT INTO {{%constructorblock_filter}} (`code`, `expansion`, `bundle`, `type`, `field`, `filter`, `label`, `queryFilter`) VALUES
('categoryportfolio', 'dictionary', 'portfoliocat', 'dictionary', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%dictionary_term}}","field":"enabled"}'),
('clients', 'contents', 'clients', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}'),
('featured', 'contents', 'featured', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}'),
('feedback', 'contents', 'portfolio', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}'),
('homeslider', 'contents', 'homeslider', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}'),
('portfolio', 'contents', 'portfolio', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}'),
('services', 'contents', 'services', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}'),
('staff', 'contents', 'employees', 'contents', 'enabled', 'boolean', 'Активность', '{"bool":"1","table":"{{%contents}}","field":"enabled"}');

INSERT INTO {{%constructorblock_sort}} (`code`, `expansion`, `bundle`, `type`, `field`, `filter`, `table`, `label`, `order`) VALUES
('clients', 'contents', 'clients', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC'),
('featured', 'contents', 'featured', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC'),
('feedback', 'contents', 'portfolio', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC'),
('homeslider', 'contents', 'homeslider', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC'),
('portfolio', 'contents', 'portfolio', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC'),
('services', 'contents', 'services', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC'),
('staff', 'contents', 'employees', 'contents', 'sort', 'defaults', '{{%contents}}', 'Порядок сортировки материала', 'ASC');

INSERT INTO {{%constructorblock_view}} (`view_id`, `code`, `expansion`, `bundle`, `enabled`, `sort`, `field_exp_id`, `show_label`, `class_wrapper`, `tag_wrapper`, `multiple_size`, `multiple_start`, `field_view`, `field_view_param`) VALUES
(1, 'homeslider', 'contents', 'homeslider', 1, 1, 3, 0, NULL, '', NULL, NULL, 'formats', 'a:4:{s:8:"decimals";s:1:"0";s:9:"dec_point";s:1:",";s:13:"thousands_sep";s:1:".";s:6:"prefix";s:0:"";}'),
(2, 'homeslider', 'contents', 'homeslider', 1, 2, 2, 0, NULL, '', NULL, NULL, 'default', ''),
(3, 'homeslider', 'contents', 'homeslider', 1, 3, 4, 0, NULL, '', NULL, NULL, 'default', ''),
(7, 'featured', 'contents', 'featured', 1, 1, 6, 0, NULL, '', NULL, NULL, 'default', ''),
(8, 'featured', 'contents', 'featured', 1, 2, 5, 0, NULL, 'h3', NULL, NULL, 'default', ''),
(9, 'featured', 'contents', 'featured', 1, 3, 7, 0, NULL, 'p', NULL, NULL, 'cut', 'a:3:{s:6:"length";s:2:"80";s:6:"prefix";s:3:"...";s:8:"stripTag";s:1:"1";}'),
(16, 'clients', 'contents', 'clients', 1, 1, 34, 0, NULL, '', NULL, NULL, 'resize', 'a:2:{s:5:"width";s:3:"180";s:6:"height";s:2:"80";}'),
(21, 'staff', 'contents', 'employees', 1, 1, 25, 0, NULL, '', NULL, NULL, 'resize', 'a:2:{s:5:"width";s:3:"590";s:6:"height";s:3:"393";}'),
(22, 'staff', 'contents', 'employees', 1, 2, 23, 0, NULL, 'h5', NULL, NULL, 'default', ''),
(23, 'staff', 'contents', 'employees', 1, 3, 24, 0, NULL, 'span', NULL, NULL, 'default', ''),
(24, 'staff', 'contents', 'employees', 1, 4, 26, 0, NULL, '', NULL, NULL, 'default', ''),
(46, 'feedback', 'contents', 'portfolio', 1, 1, 30, 0, 'happy-clients-photo', 'div', NULL, NULL, 'resize', 'a:2:{s:5:"width";s:2:"80";s:6:"height";s:2:"80";}'),
(47, 'feedback', 'contents', 'portfolio', 1, 2, 32, 0, 'happy-clients-cite', 'div', NULL, NULL, 'cut', 'a:3:{s:6:"length";s:3:"150";s:6:"prefix";s:3:"...";s:8:"stripTag";s:1:"1";}'),
(48, 'feedback', 'contents', 'portfolio', 1, 3, 31, 0, 'happy-clients-author', 'div', NULL, NULL, 'default', ''),
(52, 'services', 'contents', 'services', 1, 1, 27, 0, '', 'h3', NULL, NULL, 'default', ''),
(53, 'services', 'contents', 'services', 1, 2, 28, 0, '', '', NULL, NULL, 'default', ''),
(54, 'services', 'contents', 'services', 1, 3, 29, 0, '', 'p', NULL, NULL, 'prev', 'a:3:{s:6:"length";s:2:"50";s:6:"prefix";s:3:"...";s:8:"stripTag";s:1:"1";}'),
(56, 'categoryportfolio', 'dictionary', 'portfoliocat', 1, 1, 12, 0, '', '', NULL, NULL, 'default', ''),
(60, 'portfolio', 'contents', 'portfolio', 1, 1, 9, 0, '', '', 1, NULL, 'resize', 'a:2:{s:5:"width";s:3:"420";s:6:"height";s:3:"300";}'),
(61, 'portfolio', 'contents', 'portfolio', 1, 2, 8, 0, '', 'h5', NULL, NULL, 'default', ''),
(62, 'portfolio', 'contents', 'portfolio', 1, 3, 13, 0, '', 'span', NULL, NULL, 'default', '');

--
-- меню
--

INSERT INTO {{%menu_group}} (`id_group`, `code`, `name`, `description`, `ordering`) VALUES
(1, 'main', 'Главное меню', '', 0);

--
-- пункты меню
--

INSERT INTO {{%menu}} (`id_menu`, `id_group`, `routes_id`, `typeLink`, `name`, `image`, `paramLink`, `time_active`, `time_inactive`, `ordering`, `enabled`, `id_tmp`, `id_lang`, `id_exp`, `parent`, `home`, `param`) VALUES
(1, 1, 2, 'expansion', 'Главная', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:10:"controller";s:4:"view";s:8:"contents";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:11:"contents_id";s:1:"1";}}', NULL, NULL, 1, 1, NULL, NULL, 12, 0, 1, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(2, 1, 21, 'expansion', 'Шорткоды', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:10:"controller";s:4:"view";s:8:"contents";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:11:"contents_id";s:2:"15";}}', NULL, NULL, 2, 1, NULL, NULL, 12, 0, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(3, 1, 23, 'expansion', 'Иконки', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:10:"controller";s:4:"view";s:8:"contents";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:11:"contents_id";s:2:"16";}}', NULL, NULL, 1, 1, NULL, NULL, 12, 2, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(4, 1, 28, 'expansion', 'О нас', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:10:"controller";s:4:"view";s:8:"contents";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:11:"contents_id";s:2:"20";}}', NULL, NULL, 3, 1, NULL, NULL, 12, 0, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(5, 1, 33, 'expansion', 'Наши услуги', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:4:"type";s:4:"view";s:4:"type";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:6:"bundle";s:8:"services";}}', NULL, NULL, 1, 1, NULL, NULL, 12, 4, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(6, 1, 36, 'expansion', 'FAQ', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:4:"type";s:4:"view";s:4:"type";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:6:"bundle";s:7:"faqtype";}}', NULL, NULL, 2, 1, NULL, NULL, 12, 4, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(7, 1, 38, 'expansion', 'Порфолио', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:4:"type";s:4:"view";s:4:"type";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:6:"bundle";s:9:"portfolio";}}', NULL, NULL, 4, 1, NULL, NULL, 12, 0, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(9, 1, 40, 'expansion', 'Архитектруа', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"2";}}', NULL, NULL, 1, 1, NULL, NULL, 12, 7, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(10, 1, 41, 'expansion', 'Технологии', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"3";}}', NULL, NULL, 2, 1, NULL, NULL, 12, 7, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(11, 1, 42, 'expansion', 'Фотография', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"4";}}', NULL, NULL, 3, 1, NULL, NULL, 12, 7, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(12, 1, 43, 'expansion', 'Блог', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:4:"type";s:4:"view";s:4:"type";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:6:"bundle";s:4:"blog";}}', NULL, NULL, 5, 1, NULL, NULL, 12, 0, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(20, 1, 58, 'expansion', 'Музыка', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"1";}}', NULL, NULL, 1, 1, NULL, NULL, 12, 12, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(21, 1, 59, 'expansion', 'Технологии', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"5";}}', NULL, NULL, 2, 1, NULL, NULL, 12, 12, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(22, 1, 60, 'expansion', 'Развлекательная программа', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"6";}}', NULL, NULL, 3, 1, NULL, NULL, 12, 12, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(23, 1, 61, 'expansion', 'Живопись', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"7";}}', NULL, NULL, 4, 1, NULL, NULL, 12, 12, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(24, 1, 62, 'expansion', 'Фотография', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:8:"category";s:4:"view";s:8:"category";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:7:"term_id";s:1:"8";}}', NULL, NULL, 5, 1, NULL, NULL, 12, 12, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}'),
(25, 1, 64, 'expansion', 'Контакты', NULL, 'a:5:{s:9:"component";s:8:"contents";s:10:"controller";s:10:"controller";s:4:"view";s:8:"contents";s:6:"layout";s:7:"default";s:9:"url_param";a:1:{s:11:"contents_id";s:2:"31";}}', NULL, NULL, 6, 1, NULL, NULL, 12, 0, 0, 'a:4:{s:14:"menu_css_class";s:0:"";s:15:"menu_body_class";s:0:"";s:13:"menu_attr_rel";s:0:"";s:17:"menu_attr_onclick";s:0:"";}');

--
-- виджеты сайта
--

INSERT INTO {{%widgets}} (`id_wid`, `name`, `title`, `position`, `ordering`, `time_cache`, `enable_cache`, `time_active`, `time_inactive`, `enabled`, `enable_php`, `php_code`, `title_show`, `id_tmp`, `id_lang`, `param`) VALUES
(6, 'block', 'Слайдер главной', 'banner-top', 1, 4, 0, NULL, NULL, 1, 0, '', 0, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:10:"homeslider";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(7, 'block', 'Наши преимущества', 'featured', 3, 6, 0, NULL, NULL, 1, 0, '', 0, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:8:"featured";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(8, 'block', 'Наши последние работы', 'portfolio', 2, 5, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:9:"portfolio";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(9, 'block', 'Наши клинеты', 'before-footer', 1, 4, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:7:"clients";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(10, 'htmlcode', 'О нас', 'footer-1', 1, 7, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:2:{s:7:"wrapper";s:0:"";s:8:"htmlcode";s:302:"Критерий сходимости Коши трансформирует коллинеарный экстремум функции. Интеграл Дирихле стремительно позиционирует экспериментальный абсолютно сходящийся ряд.";}'),
(11, 'htmlcode', 'Наши контакты', 'footer-2', 1, 6, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:2:{s:7:"wrapper";s:0:"";s:8:"htmlcode";s:399:"<ul class="get-in-touch">\r\n<li><i class="icon-map-marker"></i>\r\n<p><strong>Адресс:</strong> Россия, Ставроопльский край, Ставрополь</p>\r\n</li>\r\n<li><i class="icon-user"></i>\r\n<p><strong>Телефон:</strong> +7(918)884-86-47</p>\r\n</li>\r\n<li><i class="icon-envelope-alt"></i>\r\n<p><strong>Email:</strong> <a href="#">info@maze-studio.ru</a></p>\r\n</li>\r\n</ul>";}'),
(12, 'menu', 'Верхнее меню', 'navigation', 1, 6, 0, NULL, NULL, 1, 0, '', 0, 2, 0, 'a:10:{s:7:"wrapper";s:0:"";s:6:"layout";s:3:"top";s:8:"id_group";s:1:"1";s:5:"level";s:1:"0";s:9:"parent_id";s:0:"";s:7:"subitem";s:1:"1";s:13:"active_parent";s:13:"active-parent";s:13:"active_target";s:13:"active-target";s:9:"css_class";s:0:"";s:6:"css_id";s:10:"responsive";}'),
(13, 'breadcrumb', 'Хлебные крошки', 'titlebar', 1, 6, 0, NULL, NULL, 1, 1, 'return !RC::app()->router->getIsHome();', 0, 2, 0, 'a:1:{s:7:"wrapper";s:0:"";}'),
(14, 'block', 'Сотрудники', 'portfolio', 1, 7, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:5:"staff";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(15, 'block', 'О нас пишут', 'clients', 2, 4, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:8:"feedback";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(16, 'block', 'Наши услуги', 'clients', 1, 6, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:8:"services";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(17, 'htmlcode', 'Наши достижения', 'right-contents', 4, 4, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:2:{s:7:"wrapper";s:0:"";s:8:"htmlcode";s:671:"<div id="skillzz">\r\n<div class="skill-bar"><span class="skill-title"><i class="icon-shield"></i> Security</span>\r\n<div class="skill-bar-value" style="width: 90%;"></div>\r\n</div>\r\n<div class="skill-bar"><span class="skill-title"><i class="icon-html5"></i> HTML / CSS</span>\r\n<div class="skill-bar-value" style="width: 80%;"></div>\r\n</div>\r\n<div class="skill-bar"><span class="skill-title"><i class="icon-laptop"></i> Usability</span>\r\n<div class="skill-bar-value" style="width: 70%;"></div>\r\n</div>\r\n<div class="skill-bar"><span class="skill-title"><i class="icon-puzzle-piece"></i> Marketing</span>\r\n<div class="skill-bar-value" style="width: 60%;"></div>\r\n</div>\r\n</div>";}'),
(18, 'htmlcode', 'Задать вопрос', 'right-contents', 3, 5, 0, NULL, NULL, 1, 0, '', 0, 2, 0, 'a:2:{s:7:"wrapper";s:4:"none";s:8:"htmlcode";s:287:"<div class="notice-box">\r\n<h3>Еще один вопрос?</h3>\r\n<i class="icon-envelope" style="margin-top: 5px;"></i>\r\n<p>Если у вас есть вопросы, отправьте нам сообщение и мы ответим вам как можно скорее.</p>\r\n</div>";}'),
(21, 'block', 'Категории портфолио', 'featured', 2, 5, 0, NULL, NULL, 1, 1, 'if(RC::app()->router->component == "contents"){\r\n	if(RC::app()->request->get(''bundle'') == "portfolio"){\r\n		return true;\r\n	}elseif(RC::app()->router->view == "category" && RC::app()->request->get(''term_id'')){\r\n		$term = root\\expansion\\exp_contents\\model\\ModelTerm::find(RC::app()->request->get(''term_id''));\r\n		if($term){\r\n			return $term->term->bundle == "portfoliocat";\r\n		}\r\n	}\r\n}\r\n', 0, 2, 0, 'a:5:{s:7:"wrapper";s:0:"";s:8:"block_id";s:17:"categoryportfolio";s:7:"phpcode";s:0:"";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(22, 'menu', 'Категория', 'right-contents', 1, 5, 0, NULL, NULL, 1, 1, 'if(RC::app()->router->component == "contents"){\r\n	if(RC::app()->request->get(''bundle'') == "blog"){\r\n		return true;\r\n	}elseif(RC::app()->router->view == "category" && RC::app()->request->get(''term_id'')){\r\n		$term = root\\expansion\\exp_contents\\model\\ModelTerm::find(RC::app()->request->get(''term_id''));\r\n		if($term){\r\n			return $term->term->bundle == "blogcat";\r\n		}\r\n	}elseif(RC::app()->router->view == "contents" && RC::app()->request->get(''contents_id'')){\r\n		$content = root\\expansion\\exp_contents\\model\\ModelContent::find(RC::app()->request->get(''contents_id'')); \r\n		if($content){\r\n			return $content->contents->bundle == "blog";\r\n		}\r\n	}\r\n}\r\n\r\n', 1, 2, 0, 'a:10:{s:7:"wrapper";s:0:"";s:6:"layout";s:3:"nav";s:8:"id_group";s:1:"1";s:5:"level";s:1:"0";s:9:"parent_id";s:2:"12";s:7:"subitem";s:1:"1";s:13:"active_parent";s:13:"active-parent";s:13:"active_target";s:13:"active-target";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(23, 'formsend', 'Форма обратной связи', 'after-contents', 1, 4, 0, NULL, NULL, 1, 0, '', 0, 2, 0, 'a:6:{s:7:"wrapper";s:0:"";s:5:"thema";s:23:"Новая заявка";s:5:"email";s:19:"dark-true@yandex.ru";s:11:"textsuccess";s:65:"Ваше сообщение успешно отправилось";s:9:"css_class";s:0:"";s:6:"css_id";s:0:"";}'),
(24, 'maps', 'Наш адрес', 'featured', 1, 3, 0, NULL, NULL, 1, 0, '', 0, 2, 0, 'a:20:{s:7:"wrapper";s:0:"";s:7:"country";s:12:"Россия";s:6:"region";s:37:"Ставропольский край";s:4:"city";s:20:"Ставрополь";s:6:"street";s:28:"Краснофлотская";s:5:"house";s:6:"42/117";s:5:"popup";s:1:"1";s:9:"textpopup";s:13:"Мой дом";s:4:"zoom";s:2:"15";s:11:"scrollwheel";s:1:"0";s:10:"panControl";s:1:"1";s:11:"zoomControl";s:1:"1";s:14:"mapTypeControl";s:1:"1";s:12:"scaleControl";s:1:"1";s:17:"streetViewControl";s:1:"1";s:18:"overviewMapControl";s:1:"1";s:9:"css_class";s:11:"contact-map";s:5:"width";s:4:"100%";s:6:"height";s:5:"300px";s:6:"css_id";s:0:"";}'),
(25, 'htmlcode', 'Наши контакты', 'right-contents', 2, 5, 0, NULL, NULL, 1, 0, '', 1, 2, 0, 'a:2:{s:7:"wrapper";s:0:"";s:8:"htmlcode";s:778:"<p>Форма политического сознания очевидна не для всех. Политическое учение Руссо вызывает экзистенциальный континентально-европейский тип политической культуры. Политические учения Гоббса косвенно.</p>\r\n<ul class="contact-informations">\r\n<li><span class="address">120 Seward Street</span></li>\r\n<li><span class="address">Oklahoma City, USA</span></li>\r\n</ul>\r\n<ul class="contact-informations second">\r\n<li><i class="icon-user"></i>\r\n<p>+7(918)884-8647</p>\r\n</li>\r\n<li><i class="icon-envelope-alt"></i>\r\n<p>info@maze-studio.ru</p>\r\n</li>\r\n<li><i class="icon-globe"></i>\r\n<p>www.maze-studio.ru</p>\r\n</li>\r\n</ul>";}');

--
-- связь пункты меню виджеты
--

INSERT INTO {{%widgets_menu}} (`id_wid`, `id_menu`) VALUES
(6, 1),
(7, 1),
(9, 1),
(16, 1),
(14, 4),
(15, 4),
(17, 4),
(9, 5),
(18, 6),
(23, 25),
(24, 25),
(25, 25);

--
--  фильтры url виджеты
--
INSERT INTO {{%widgets_url}} (`url_id`, `id_wid`, `method`, `name`, `value`, `sort`, `visible`) VALUES
(9, 8, 'url', NULL, '/porfolio/fotografiya/*', 1, 1),
(10, 8, 'url', NULL, '/porfolio/arhitektrua/*', 2, 1),
(11, 8, 'url', NULL, '/porfolio/tehnologii/*', 3, 1),
(12, 8, 'url', NULL, '/', 4, 1);


CREATE TABLE {{%sitemap}} (
  `sitemap_id` int(11)  AUTO_INCREMENT NOT NULL,
  `routes_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `enable_xml` tinyint(1) DEFAULT '0',
  `enable_html` tinyint(1) DEFAULT '0',
  `date_create` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
   `params` longtext DEFAULT NULL,
   PRIMARY KEY (`sitemap_id`),
   FOREIGN KEY (`routes_id`) REFERENCES  {{%routes}} (`routes_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE {{%sitemap_link}} (
  `link_id` int(11)  AUTO_INCREMENT NOT NULL,
  `sitemap_id` int(11)  NOT NULL,
  `title` varchar(255) NOT NULL,
  `enabled` tinyint(1) DEFAULT '0',
  `id` int(11)  DEFAULT NULL,
  `expansion` varchar(255) NOT NULL,
  `loc` varchar(2048) NOT NULL,
  `lastmod` datetime DEFAULT NULL,
  `changefreq` varchar(20) NOT NULL,
  `priority` tinytext NOT NULL,
  PRIMARY KEY(`link_id`),
  FOREIGN KEY (`sitemap_id`) REFERENCES  {{%sitemap}} (`sitemap_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE {{%sitemap_robots}} (
  `robots_id` int(11)  AUTO_INCREMENT NOT NULL,
  `title` varchar(255) NOT NULL,
  `images` varchar(1000) DEFAULT NULL,
  `search` varchar(255) DEFAULT NULL,
   PRIMARY KEY(`robots_id`)
);

CREATE TABLE {{%sitemap_visits}} (
  `visits_id` int(11) AUTO_INCREMENT NOT NULL,
  `sitemap_id` int(11)  NOT NULL,
  `robots_id` int(11)  DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `ip` varchar(200) DEFAULT NULL,
  `agent` varchar(600) DEFAULT NULL,
  `date_visits` datetime DEFAULT NULL,
   PRIMARY KEY(`visits_id`),
   FOREIGN KEY (`sitemap_id`) REFERENCES  {{%sitemap}} (`sitemap_id`) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY (`robots_id`) REFERENCES  {{%sitemap_robots}} (`robots_id`) ON DELETE CASCADE ON UPDATE CASCADE
);




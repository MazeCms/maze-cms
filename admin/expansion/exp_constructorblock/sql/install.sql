
CREATE TABLE {{%constructorblock_block}} (
  `code` varchar(50) NOT NULL,
  `bundle` varchar(128) NOT NULL,
  `expansion` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `list` tinyint(1) DEFAULT '0',
  `multiple_size` int(11) DEFAULT NULL,
  `multiple_start` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
   PRIMARY KEY (`code`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%constructorblock_filter}} (
  `code` varchar(50) NOT NULL,
  `expansion` varchar(255) NOT NULL,
  `bundle` varchar(128) NOT NULL,
  `type` varchar(255) NOT NULL,
  `field` varchar(128) NOT NULL,
  `filter` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `queryFilter` longtext,
  PRIMARY KEY(`code`, `expansion`, `bundle`, `field`),
  FOREIGN KEY (`code`) REFERENCES  {{%constructorblock_block}} (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%constructorblock_sort}} (
  `code` varchar(50) NOT NULL,
  `expansion` varchar(255) NOT NULL,
  `bundle` varchar(128) NOT NULL,
  `type` varchar(255) NOT NULL,
  `field` varchar(128) NOT NULL,
  `filter` varchar(100) NOT NULL,
  `table` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` varchar(5) NOT NULL,
   PRIMARY KEY(`code`, `expansion`, `bundle`, `field`),
   FOREIGN KEY (`code`) REFERENCES  {{%constructorblock_block}} (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {{%constructorblock_view}} (
  `view_id` int(11)  AUTO_INCREMENT NOT NULL,
  `code` varchar(50) NOT NULL,
  `expansion` varchar(255) NOT NULL,
  `bundle` varchar(128) NOT NULL,
  `enabled` tinyint(1) DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `field_exp_id` int(11) DEFAULT NULL,
  `show_label` tinyint(1) DEFAULT '0',
  `class_wrapper` varchar(255) DEFAULT NULL,
  `tag_wrapper` varchar(10) NOT NULL,
  `multiple_size` int(11) DEFAULT NULL,
  `multiple_start` int(11) DEFAULT NULL,
  `field_view` varchar(53) DEFAULT NULL,
  `field_view_param` longtext,
   PRIMARY KEY(`view_id`),
   FOREIGN KEY (`code`) REFERENCES  {{%constructorblock_block}} (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (`field_exp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


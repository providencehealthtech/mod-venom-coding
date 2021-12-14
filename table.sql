--
-- Table structure for table `venom_codes`
--

DROP TABLE IF EXISTS `venom_dx`;
CREATE TABLE `venom_dx` (
  `dict_id` int,
  `term` varchar(255),
  `approved` int default NULL,
  `active` int default NULL,
  `subset_id` int default NULL,
  `subset` varchar(255) default NULL,
  `first_release` varchar(3) default NULL,
  `top_level_model` text default NULL,
  `large` tinyint default NULL,
  `small` tinyint default NULL,
  `farm` tinyint default NULL,
  `exotic` tinyint default NULL,
  `equine` tinyint default NULL,
  `revision` int default 0,
  PRIMARY KEY  (`dict_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `venom_dx_test`;
CREATE TABLE `venom_dx_test` (
  `dict_id` int,
  `term` varchar(255),
  `approved` int default NULL,
  `active` int default NULL,
  `subset_id` int default NULL,
  `subset` varchar(255) default NULL,
  `first_release` varchar(3) default NULL,
  `top_level_model` text default NULL,
  `large` tinyint default NULL,
  `small` tinyint default NULL,
  `farm` tinyint default NULL,
  `exotic` tinyint default NULL,
  `equine` tinyint default NULL,
  `revision` int default 0,
  PRIMARY KEY  (`dict_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `venom_proc`;
CREATE TABLE `venom_proc` (
  `dict_id` int,
  `term` varchar(255),
  `approved` int default NULL,
  `active` int default NULL,
  `subset_id` int default NULL,
  `subset` varchar(255) default NULL,
  `first_release` varchar(3) default NULL,
  `top_level_model` text default NULL,
  `large` tinyint default NULL,
  `small` tinyint default NULL,
  `farm` tinyint default NULL,
  `exotic` tinyint default NULL,
  `equine` tinyint default NULL,
  `revision` int default 0,
  PRIMARY KEY  (`dict_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `venom_admin`;
CREATE TABLE `venom_admin` (
  `dict_id` int,
  `term` varchar(255),
  `approved` int default NULL,
  `active` int default NULL,
  `subset_id` int default NULL,
  `subset` varchar(255) default NULL,
  `first_release` varchar(3) default NULL,
  `top_level_model` text default NULL,
  `large` tinyint default NULL,
  `small` tinyint default NULL,
  `farm` tinyint default NULL,
  `exotic` tinyint default NULL,
  `equine` tinyint default NULL,
  `revision` int default 0,
  PRIMARY KEY  (`dict_id`)
) ENGINE=InnoDB;

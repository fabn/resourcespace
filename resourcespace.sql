-- MySQL dump 10.10
--
-- Host: localhost    Database: rs13
-- ------------------------------------------------------
-- Server version	5.0.15-standard

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `collection`
--

DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `ref` int(11) NOT NULL auto_increment,
  `name` varchar(100) character set latin1 default NULL,
  `user` int(11) default NULL,
  `created` datetime default NULL,
  `public` int(11) NOT NULL default '0',
  `theme` varchar(100) character set latin1 default NULL,
  `allow_changes` int(11) default '0',
  `cant_delete` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ref`),
  KEY `theme` (`theme`),
  KEY `public` (`public`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `collection`
--


/*!40000 ALTER TABLE `collection` DISABLE KEYS */;
LOCK TABLES `collection` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `collection` ENABLE KEYS */;

--
-- Table structure for table `collection_resource`
--

DROP TABLE IF EXISTS `collection_resource`;
CREATE TABLE `collection_resource` (
  `collection` int(11) default NULL,
  `resource` int(11) default NULL,
  `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY `collection` (`collection`),
  KEY `resource_collection` (`collection`,`resource`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `collection_resource`
--


/*!40000 ALTER TABLE `collection_resource` DISABLE KEYS */;
LOCK TABLES `collection_resource` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `collection_resource` ENABLE KEYS */;

--
-- Table structure for table `collection_savedsearch`
--

DROP TABLE IF EXISTS `collection_savedsearch`;
CREATE TABLE `collection_savedsearch` (
  `collection` int(11) default NULL,
  `search` text character set latin1,
  `restypes` text character set latin1,
  `archive` int(11) default NULL,
  `ref` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `collection_savedsearch`
--


/*!40000 ALTER TABLE `collection_savedsearch` DISABLE KEYS */;
LOCK TABLES `collection_savedsearch` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `collection_savedsearch` ENABLE KEYS */;

--
-- Table structure for table `daily_stat`
--

DROP TABLE IF EXISTS `daily_stat`;
CREATE TABLE `daily_stat` (
  `year` int(11) default NULL,
  `month` int(11) default NULL,
  `day` int(11) default NULL,
  `activity_type` varchar(50) character set latin1 default NULL,
  `object_ref` int(11) default NULL,
  `count` int(11) default '0',
  KEY `stat_day` (`year`,`month`,`day`),
  KEY `stat_month` (`year`,`month`),
  KEY `stat_day_activity` (`year`,`month`,`day`,`activity_type`),
  KEY `stat_day_activity_ref` (`year`,`month`,`day`,`activity_type`,`object_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `daily_stat`
--


/*!40000 ALTER TABLE `daily_stat` DISABLE KEYS */;
LOCK TABLES `daily_stat` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `daily_stat` ENABLE KEYS */;

--
-- Table structure for table `external_access_keys`
--

DROP TABLE IF EXISTS `external_access_keys`;
CREATE TABLE `external_access_keys` (
  `resource` int(11) default NULL,
  `access_key` char(10) default NULL,
  KEY `resource` (`resource`),
  KEY `resource_key` (`resource`,`access_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `external_access_keys`
--


/*!40000 ALTER TABLE `external_access_keys` DISABLE KEYS */;
LOCK TABLES `external_access_keys` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `external_access_keys` ENABLE KEYS */;

--
-- Table structure for table `keyword`
--

DROP TABLE IF EXISTS `keyword`;
CREATE TABLE `keyword` (
  `ref` int(11) NOT NULL auto_increment,
  `keyword` varchar(100) character set latin1 default NULL,
  `soundex` varchar(10) character set latin1 default NULL,
  `hit_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ref`),
  KEY `keyword` (`keyword`),
  KEY `keyword_hit_count` (`hit_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `keyword`
--


/*!40000 ALTER TABLE `keyword` DISABLE KEYS */;
LOCK TABLES `keyword` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `keyword` ENABLE KEYS */;

--
-- Table structure for table `preview_size`
--

DROP TABLE IF EXISTS `preview_size`;
CREATE TABLE `preview_size` (
  `id` char(3) character set latin1 default NULL,
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `padtosize` int(11) default '0',
  `name` varchar(50) character set latin1 default NULL,
  `internal` int(11) default '0',
  `allow_preview` int(11) default '0',
  `allow_restricted` int(11) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `preview_size`
--


/*!40000 ALTER TABLE `preview_size` DISABLE KEYS */;
LOCK TABLES `preview_size` WRITE;
INSERT INTO `preview_size` VALUES ('thm',150,150,0,'Thumbnail',1,0,0),('pre',350,350,0,'Preview',1,0,1),('scr',800,800,0,'Screen',0,1,0),('lpr',9000,9000,0,'Printable',0,0,0),('hpr',999999,999999,0,'High resolution print',0,0,0),('col',75,75,0,'Collection',1,0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `preview_size` ENABLE KEYS */;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `ref` int(11) NOT NULL auto_increment,
  `name` varchar(100) character set latin1 default NULL,
  `query` text character set latin1,
  PRIMARY KEY  (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `report`
--


/*!40000 ALTER TABLE `report` DISABLE KEYS */;
LOCK TABLES `report` WRITE;
INSERT INTO `report` VALUES (1,'Keywords used in resource edits','select k.keyword \'Keyword\',sum(count) \'Entered Count\' from keyword k,daily_stat d where k.ref=d.object_ref and d.activity_type=\'Keyword added to resource\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by k.ref order by \'Entered Count\' desc limit 100;\r\n'),(2,'Keywords used in searches','select k.keyword \'Keyword\',sum(count) Searches from keyword k,daily_stat d where k.ref=d.object_ref and d.activity_type=\'Keyword usage\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by k.ref order by Searches desc\r\n'),(3,'Resource downloads','select r.ref \'Resource ID\',r.title \'Title\',sum(count) Downloads from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=\'Resource download\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by r.ref order by Downloads desc;\r\n'),(4,'Resource views','select r.ref \'Resource ID\',r.title \'Title\',sum(count) Views from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=\'Resource view\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by r.ref order by Views desc;\r\n'),(5,'Resources sent via e-mail','select r.ref \'Resource ID\',r.title \'Title\',sum(count) Sent from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=\'E-mailed resource\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by r.ref order by Sent desc;\r\n'),(6,'Resources added to collection','select r.ref \'Resource ID\',r.title \'Title\',sum(count) Added from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=\'Add resource to collection\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by r.ref order by Added desc;\r\n'),(7,'Resources created','select ref \'Resource ID\',title \'Title\',creation_date \'Creation Date\' from resource where creation_date>=date(\'[from-y]-[from-m]-[from-d]\') and creation_date<=adddate(date(\'[to-y]-[to-m]-[to-d]\'),1)'),(8,'Resources with zero downloads','select ref \'Resource ID\',title \'Title\' from resource where ref not in \r\n\r\n(\r\n#Previous query to fetch resource downloads\r\nselect r.ref from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=\'Resource download\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\n\r\ngroup by r.ref\r\n)'),(9,'Resources with zero views','select ref \'Resource ID\',title \'Title\' from resource where ref not in \r\n\r\n(\r\n#Previous query to fetch resource views\r\nselect r.ref from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=\'Resource view\'\r\n\r\n# --- date ranges\r\n# Make sure date is greater than FROM date\r\nand \r\n(\r\nd.year>[from-y]\r\nor \r\n(d.year=[from-y] and d.month>[from-m])\r\nor\r\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\r\n)\r\n# Make sure date is less than TO date\r\nand\r\n(\r\nd.year<[to-y]\r\nor \r\n(d.year=[to-y] and d.month<[to-m])\r\nor\r\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\r\n)\r\n\r\ngroup by r.ref\r\n)');
UNLOCK TABLES;
/*!40000 ALTER TABLE `report` ENABLE KEYS */;

--
-- Table structure for table `research_request`
--

DROP TABLE IF EXISTS `research_request`;
CREATE TABLE `research_request` (
  `ref` int(11) NOT NULL auto_increment,
  `name` text character set latin1,
  `description` text character set latin1,
  `deadline` datetime default NULL,
  `contact` varchar(100) character set latin1 default NULL,
  `finaluse` text character set latin1,
  `resource_types` varchar(50) character set latin1 default NULL,
  `noresources` int(11) default NULL,
  `shape` varchar(50) character set latin1 default NULL,
  `created` datetime default NULL,
  `user` int(11) default NULL,
  `assigned_to` int(11) default NULL,
  `status` int(11) NOT NULL default '0',
  `collection` int(11) default NULL,
  PRIMARY KEY  (`ref`),
  KEY `research_collections` (`collection`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `research_request`
--


/*!40000 ALTER TABLE `research_request` DISABLE KEYS */;
LOCK TABLES `research_request` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `research_request` ENABLE KEYS */;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `ref` int(11) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `resource_type` int(11) default NULL,
  `has_image` int(11) default '0',
  `hit_count` int(11) NOT NULL default '0',
  `creation_date` datetime default NULL,
  `rating` int(11) default NULL,
  `country` varchar(200) default NULL,
  `file_extension` varchar(10) character set latin1 default NULL,
  `preview_extension` varchar(10) default NULL,
  `image_red` int(11) default NULL,
  `image_green` int(11) default NULL,
  `image_blue` int(11) default NULL,
  `thumb_width` int(11) default NULL,
  `thumb_height` int(11) default NULL,
  `archive` int(11) default '0',
  `access` int(11) default '0',
  `colour_key` varchar(5) character set latin1 default NULL,
  `created_by` int(11) default NULL,
  `file_path` varchar(500) default NULL,
  `file_modified` datetime default NULL,
  PRIMARY KEY  (`ref`),
  KEY `hit_count` (`hit_count`),
  KEY `resource_archive` (`archive`),
  KEY `resource_access` (`access`),
  KEY `resource_type` (`resource_type`),
  KEY `resource_creation_date` (`creation_date`),
  KEY `rating` (`rating`),
  KEY `colour_key` (`colour_key`),
  KEY `file_path` (`file_path`(333)),
  KEY `archive` (`archive`),
  KEY `has_image` (`has_image`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource`
--


/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
LOCK TABLES `resource` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;

--
-- Table structure for table `resource_custom_access`
--

DROP TABLE IF EXISTS `resource_custom_access`;
CREATE TABLE `resource_custom_access` (
  `resource` int(11) default NULL,
  `usergroup` int(11) default NULL,
  `access` int(11) default NULL,
  KEY `resource` (`resource`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `resource_custom_access`
--


/*!40000 ALTER TABLE `resource_custom_access` DISABLE KEYS */;
LOCK TABLES `resource_custom_access` WRITE;
INSERT INTO `resource_custom_access` VALUES (0,0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_custom_access` ENABLE KEYS */;

--
-- Table structure for table `resource_data`
--

DROP TABLE IF EXISTS `resource_data`;
CREATE TABLE `resource_data` (
  `resource` int(11) default NULL,
  `resource_type_field` int(11) default NULL,
  `value` text character set latin1,
  KEY `resource_type_field` (`resource_type_field`),
  KEY `resource` (`resource`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_data`
--


/*!40000 ALTER TABLE `resource_data` DISABLE KEYS */;
LOCK TABLES `resource_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_data` ENABLE KEYS */;

--
-- Table structure for table `resource_keyword`
--

DROP TABLE IF EXISTS `resource_keyword`;
CREATE TABLE `resource_keyword` (
  `resource` int(11) NOT NULL,
  `keyword` int(11) NOT NULL,
  `hit_count` int(11) default '0',
  `position` int(11) default '0',
  `resource_type_field` int(11) default '0',
  `new_hit_count` int(11) NOT NULL default '0',
  KEY `resource_keyword` (`resource`,`keyword`),
  KEY `resource` (`resource`),
  KEY `keyword` (`keyword`),
  KEY `resource_type_field` (`resource_type_field`),
  KEY `rk_all` (`resource`,`keyword`,`resource_type_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_keyword`
--


/*!40000 ALTER TABLE `resource_keyword` DISABLE KEYS */;
LOCK TABLES `resource_keyword` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_keyword` ENABLE KEYS */;

--
-- Table structure for table `resource_log`
--

DROP TABLE IF EXISTS `resource_log`;
CREATE TABLE `resource_log` (
  `date` datetime default NULL,
  `user` int(11) default NULL,
  `resource` int(11) default NULL,
  `type` char(1) character set latin1 default NULL,
  `resource_type_field` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_log`
--


/*!40000 ALTER TABLE `resource_log` DISABLE KEYS */;
LOCK TABLES `resource_log` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_log` ENABLE KEYS */;

--
-- Table structure for table `resource_related`
--

DROP TABLE IF EXISTS `resource_related`;
CREATE TABLE `resource_related` (
  `resource` int(11) NOT NULL,
  `related` int(11) NOT NULL,
  KEY `resource_related` (`resource`),
  KEY `related` (`related`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_related`
--


/*!40000 ALTER TABLE `resource_related` DISABLE KEYS */;
LOCK TABLES `resource_related` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_related` ENABLE KEYS */;

--
-- Table structure for table `resource_type`
--

DROP TABLE IF EXISTS `resource_type`;
CREATE TABLE `resource_type` (
  `ref` int(11) NOT NULL auto_increment,
  `name` varchar(50) character set latin1 default NULL,
  PRIMARY KEY  (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_type`
--


/*!40000 ALTER TABLE `resource_type` DISABLE KEYS */;
LOCK TABLES `resource_type` WRITE;
INSERT INTO `resource_type` VALUES (1,'Photo'),(4,'Audio'),(3,'Video');
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_type` ENABLE KEYS */;

--
-- Table structure for table `resource_type_field`
--

DROP TABLE IF EXISTS `resource_type_field`;
CREATE TABLE `resource_type_field` (
  `ref` int(11) NOT NULL auto_increment,
  `name` varchar(50) character set latin1 default NULL,
  `title` varchar(50) character set latin1 default NULL,
  `type` int(11) default NULL,
  `options` text character set latin1,
  `order_by` int(11) NOT NULL default '0',
  `keywords_index` int(11) NOT NULL default '0',
  `resource_type` int(11) NOT NULL default '0',
  `resource_column` varchar(50) character set latin1 default NULL,
  `display_field` int(11) default '1',
  `use_for_similar` int(11) default '1',
  `iptc_equiv` varchar(20) character set latin1 default NULL,
  `display_template` text character set latin1,
  `tab_name` varchar(50) default '',
  PRIMARY KEY  (`ref`),
  KEY `resource_type` (`resource_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_type_field`
--


/*!40000 ALTER TABLE `resource_type_field` DISABLE KEYS */;
LOCK TABLES `resource_type_field` WRITE;
INSERT INTO `resource_type_field` VALUES (1,'keywords','Keywords',1,'',50,1,0,NULL,1,1,'2#025','',''),(8,'title','Title',0,'',5,1,0,'title',0,1,'2#005','',''),(9,'extract','Story Extract',1,'',7,0,2,NULL,1,0,'','<div class=\"RecordStory\">\r\n  <h1>[title]</h1>\r\n  <p>[value]</p>\r\n<!--  <p><a href=\"story_print.php?ref=<?=$ref?>\" target=\"_new\">Print Story Text</a></p>-->\r\n</div>',''),(10,'credit','Credit',0,'',300,1,0,NULL,1,1,'2#080',NULL,''),(11,'','Rating',3,'0,1,2,3,4,5',150,0,0,'rating',0,1,NULL,NULL,''),(12,'','Date',4,'',160,1,0,'creation_date',1,1,'2#055','',''),(18,'caption','Caption',5,'',55,1,0,NULL,1,0,'2#120','<div class=\"item\"><h3>[title]</h3><p>[value]</p></div>\r\n<div class=\"clearerleft\"> </div>',''),(25,'','Notes',1,'',1500,0,0,NULL,1,0,'2#103','<div class=\"RecordStory\">\r\n  <h1>[title]</h1>\r\n  <p>[value]</p>\r\n<!--  <p><a href=\"story_print.php?ref=<?=$ref?>\" target=\"_new\">Print Story Text</a></p>-->\r\n</div>',''),(29,'person','Named Person(s)',0,'',60,1,0,NULL,1,1,NULL,NULL,''),(52,'camera','Camera Make / Model',0,'',1600,0,1,NULL,1,0,'','',''),(51,'original_filename','Original Filename',0,'',6,1,0,'file_path',0,1,'','',''),(53,'vidcontents','Video Contents List',1,'',9999,1,3,NULL,1,1,'','',''),(54,'source','Source',3,'Digital Camera, Scanned Negative, Scanned Photo',1601,0,1,NULL,1,1,'','','');
UNLOCK TABLES;
/*!40000 ALTER TABLE `resource_type_field` ENABLE KEYS */;

--
-- Table structure for table `site_text`
--

DROP TABLE IF EXISTS `site_text`;
CREATE TABLE `site_text` (
  `page` varchar(50) character set latin1 default NULL,
  `name` varchar(50) character set latin1 default NULL,
  `text` text character set latin1,
  `ref` int(11) NOT NULL auto_increment,
  `language` varchar(10) character set latin1 default NULL,
  `ignore_me` int(11) default NULL,
  `specific_to_group` int(11) default NULL,
  PRIMARY KEY  (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_text`
--


/*!40000 ALTER TABLE `site_text` DISABLE KEYS */;
LOCK TABLES `site_text` WRITE;
INSERT INTO `site_text` VALUES ('collection_public','introtext','Public collections are created by other users.',73,'en',NULL,NULL),('all','searchpanel','Search using descriptions, keywords and resource numbers',2,'en',NULL,NULL),('home','themes','The very best resources, hand picked and grouped.',3,'en',NULL,NULL),('home','mycollections','Organise, collaborate & share your resources. Use these tools to help you work more effectively.',4,'en',NULL,NULL),('home','help','Help and advice to get the most out of ResourceSpace.',5,'en',NULL,NULL),('home','welcometitle','Welcome to ResourceSpace',6,'en',NULL,NULL),('home','welcometext','Your introductory text here.',7,'en',NULL,NULL),('themes','introtext','Themes are groups of resources.',8,'en',NULL,NULL),('edit','multiple','Please select which fields you wish to overwrite. Fields you do not select will be left untouched.',60,'en',NULL,NULL),('team_archive','introtext','To edit individual archive resources, simply search for the resource, and click edit in the â€˜Resource Toolâ€™ panel on the resource screen. All resources that are ready to be archived are listed Resources Pending list. From this list it is possible to add further information and transfer the resource record into the archive. ',22,'en',NULL,NULL),('research_request','introtext','Our professional researchers are here to assist you in finding the very best resources for your projects. Complete this form as thoroughly as possible so weâ€™re able to meet your criteria accurately. <br><br>A member of the research team will be assigned to your request. Weâ€™ll keep in contact via email throughout the process, and once weâ€™ve completed the research youâ€™ll receive an email with a link to all the resources that we recommend.  ',9,'en',NULL,NULL),('collection_manage','introtext','Organise and manage your work by grouping resources together. Create â€˜Collectionsâ€™ to suit your way of working. You may want to group resources under projects that you are working on independently, share resources amongst a project team or simply keep your favourite resources together in one place. All the collections in your list appear in the â€˜My Collectionsâ€™ panel at the bottom of the screen.',10,'en',NULL,NULL),('collection_manage','findpublic','Public collections are groups of resources made widely available by users of the system. Enter a collection ID, or all or part of a collection name or username to find public collections. Add them to your list of collections to access the resources.',11,'en',NULL,NULL),('team_home','introtext','Welcome to the team centre. Use the links below to administer resources, respond to resource requests, manage themes and alter system settings.',12,'en',NULL,NULL),('help','introtext','Get the most out of ResourceSpace. These guides will help you use the system and the resources more effectively. </p>\r\n\r\n<p>Use \"Themes\" to browse resources by theme or use the simple search box to search for specific resources.</p>\r\n\r\n<p><a href=\"http://www.montala.net/downloads/ResourceSpace-GettingStarted.pdf\">Download Neale\'s user guide (PDF file)</a>',13,'en',NULL,NULL),('terms and conditions','terms and conditions','Your terms and conditions go here.',27,'en',NULL,NULL),('contribute','introtext','You can contribute your own resources. When you initially create a resource it is in the \"Pending Submission\" status. When you have uploaded your file and edited the fields, set the status field to \"Pending Review\". It will then be reviewed by the resources team.',62,'en',NULL,NULL),('done','user_password','An e-mail containing your username and password has been sent.',50,'en',NULL,NULL),('user_password','introtext','Enter your e-mail address and your username and password will be sent to you.',51,'en',NULL,NULL),('edit','batch','Please specify the default content and keywords for the resources you are about to upload. This is typically content that is common among all the resources such as country, project code, credit etc.</p>\r\n<p><b>Please note:</b> Batch uploading is a bandwidth and CPU intensive process and it is advisable to run only one upload at any given time.',52,'en',NULL,NULL),('team_copy','introtext','Enter the ID of the resource you would like to copy. Only the resource data will be copied - any uploaded file will not be copied.',53,'en',NULL,NULL),('delete','introtext','Please enter your password to confirm that you would like to delete this resource.',54,'en',NULL,NULL),('team_report','introtext','Please choose a report and a date range. The report can be opened in Microsoft Excel or similar spreadsheet application.',55,'en',NULL,NULL),('terms','introtext','Before you proceed you must accept the terms and conditions.\r\n',56,'en',NULL,NULL),('download_progress','introtext','Your download will start shortly. When your download completes, use the links below to continue.',57,'en',NULL,NULL),('view','storyextract','Story extract:',58,'en',NULL,NULL),('contact','contact','Your contact details here.',14,'en',NULL,NULL),('search_advanced','introtext','<strong>Search Tip</strong><br>Any section that you leave blank, or unticked will include ALL those terms in the search. For example, if you leave all the country boxes empty, the search will return results from all those countries. If you select only â€˜Africaâ€™ then the results will ONLY contain resources from â€˜Africaâ€™. ',15,'en',NULL,NULL),('all','researchrequest','Let our resources team find the resources you need.',17,'en',NULL,NULL),('done','research_request','A member of the research team will be assigned to your request. Weâ€™ll keep in contact via email throughout the process, and once weâ€™ve completed the research youâ€™ll receive an email with a link to all the resources that we recommend.',18,'en',NULL,NULL),('done','collection_email','An email containing a link to the collection has been sent to the users you specified. The collection has been added to their \'Collections\' list.',19,'en',NULL,NULL),('done','resource_email','An email containing a link to the resource has been sent to the users you specified.',20,'en',NULL,NULL),('themes','manage','Organise and edit the themes available online. Themes are specially promoted collections. <br><br> <strong>1 To create a new entry under a Theme -  build a collection</strong><br> Choose <strong>My Collections</strong> from the main top menu and set up a brand new <strong>public</strong> collection. Remember to include a theme name during the setup. Use an existing theme name to group the collection under a current theme (make sure you type it exactly the same), or choose a new title to create a brand new theme. Never allow users to add/remove resources from themed collections. <br> <br><strong>2 To edit the content of an existing entry under a theme </strong><br> Choose <strong>edit collection</strong>. The items in that collection will appear in the <strong>My Collections</strong> panel at the bottom of the screen. Use the standard tools to edit, remove or add resources. <br> <br><strong>3 To alter a theme name or move a collection to appear under a different theme</strong><br> Choose <strong>edit properties</strong> and edit theme category or collection name. Use an existing theme name to group the collection under an current theme (make sure you type it exactly the same), or choose a new title to create a brand new theme. <br> <br><strong>4 To remove a collection from a theme </strong><br> Choose <strong>edit properties</strong> and delete the words in the theme category box. ',21,'en',NULL,NULL),('terms','terms','Your terms and conditions go here.',26,'en',NULL,NULL),('done','resource_request','Your request has been submitted and we will be in contact shortly.',28,'en',NULL,NULL),('user_request','introtext','Please complete the form below to request a user account.',29,'en',NULL,NULL),('themes','findpublic','Public collections are collections of resources that have been shared by other users.',61,'en',NULL,NULL),('done','user_request','Your request for a user account has been sent. Your login details will be sent to you shortly.',30,'en',NULL,NULL),('about','about','Your about text goes here.',31,'en',NULL,NULL),('team_content','introtext','',32,'en',NULL,NULL),('done','deleted','The resource has been deleted.',33,'en',NULL,NULL),('upload','introtext','Upload a file using the form below.',34,'en',NULL,NULL),('home','restrictedtitle','<h1>Welcome to ResourceSpace</h1>',35,'en',NULL,NULL),('home','restrictedtext','Please click on the link that you were e-mailed to access the resources selected for you.',36,'en',NULL,NULL),('resource_email','introtext','Quickly share this resource with other users by email. A link is automatically sent out. You can also include any message as part of the email.',37,'en',NULL,NULL),('team_resource','introtext','Add individual resources or batch upload resources. To edit individual resources, simply search for the resource, and click edit in the â€˜Resource Toolâ€™ panel on the resource screen.',38,'en',NULL,NULL),('team_user','introtext','Use this section to add, remove and modify users.',39,'en',NULL,NULL),('team_research','introtext','Organise and manage â€˜Research Requestsâ€™. <br><br>Choose â€˜edit researchâ€™ to review the request details and assign the research to a team member. It is possible to base a research request on a previous collection by entering the collection ID in the â€˜editâ€™ screen. <br><br>Once the research request is assigned, choose â€˜edit collectionâ€™ to add the research request to â€˜My collectionâ€™ panel. Using the standard tools, it is then possible to add resources to the research. <br><br>Once the research is complete, choose â€˜edit researchâ€™,  change the status to complete and an email is automatically  sent to the user who requested the research. The email contains a link to the research and it is also automatically added to their â€˜My Collectionâ€™ panel.',41,'en',NULL,NULL),('collection_edit','introtext','Organise and manage your work by grouping resources together. Create â€˜Collectionsâ€™ to suit your way of working.\r\n<br>\r\nAll the collections in your list appear in the â€˜My Collectionsâ€™ panel at the bottom of the screen\r\n<br><br>\r\n<strong>Private Access</strong> allows only you and and selected users to see the collection. Ideal for grouping resources under projects that you are working on independently and share resources amongst a project team.\r\n<br><br>\r\n<strong>Public Access</strong> allows all users of the system to search and see the collection. Useful if you wish to share collections of resources that you think others would benefit from using.\r\n<br><br>\r\nYou can choose whether you allow other users (public or users you have added to your private collection) to add and remove resources or simply view them for reference.',42,'en',NULL,NULL),('team_stats','introtext','Charts are generated on demand based on live data. Tick the box to print all charts for your selected year.',43,'en',NULL,NULL),('resource_request','introtext','The resource you requested is not available online. The resource information is automatically included in the email but you can add additional comments if you wish.',44,'en',NULL,NULL),('team_batch','introtext','You can upload a batch of resource files by placing the files on a suitable FTP server. Once you have finished the files can be deleted from the FTP server.',45,'en',NULL,NULL),('team_batch_upload','introtext','You can upload a batch of resource files by placing the files on a suitable FTP server. Once you have finished the files can be deleted from the FTP server.',46,'en',NULL,NULL),('team_batch_select','introtext','You can upload a batch of resource files by placing the files on a suitable FTP server. Once you have finished the files can be deleted from the FTP server.',47,'en',NULL,NULL),('download_click','introtext','To download the resource file, right click the link below and choose \"Save As...\". You will then be asked where you would like to save the file. To open the file in your browser simply click the link.',48,'en',NULL,NULL),('collection_manage','newcollection','To create a new collection, enter a short name.',49,'en',NULL,NULL),('collection_email','introtext','Complete the form below to e-mail this collection. The user(s) will receive a link to the resource rather than file attachments so they can choose and download the appropriate resources.',59,'en',NULL,NULL),('all','footer','Powered by <a href=\"http://www.montala.net/resourcespace.php\">ResourceSpace</a> - Dan Huby and Neale Hall 2006/2007',63,'en',NULL,NULL),('change_language','introtext','Please select your language below.',72,'en',NULL,NULL),('all','searchpanel','Search using descriptions, keywords and resource numbers',67,'es',NULL,NULL),('all','footer','Accionado por el <a href=\"http://www.montala.net/resourcespace.php\">ResourceSpace</a>',65,'es',NULL,NULL),('all','researchrequest','Let our resources team find the resources you need.',66,'es',NULL,NULL),('delete','introtext',NULL,68,'es',NULL,NULL),('contribute','introtext','You can contribute your own resources. When you initially create a resource it is in the \"Pending Submission\" status. When you have uploaded your file and edited the fields, set the status field to \"Pending Review\". It will then be reviewed by the resources team.',69,'es',NULL,NULL),('done','deleted','The resource has been deleted.',70,'es',NULL,NULL),('change_password','introtext','Enter a new password below to change your password.',71,'en',NULL,NULL),('collection_manage','findpublic','Public collections are groups of resources made widely available by users of the system. Enter a collection ID, or all or part of a collection name or username to find public collections. Add them to your list of collections to access the resources.',74,'es',NULL,NULL),('themes','findpublic','Powered by <a href=\"http://www.montala.net/resourcespace.php\">ResourceSpace</a>',75,'es',NULL,NULL),('login','welcomelogin','Welcome to ResourceSpace, please log in...',76,'en',NULL,NULL),('all','footer','Desarrollado por <a href=\"http://www.montala.net/resourcespace.php\">ResourceSpace</a>',77,'es',62,NULL),('all','researchrequest','PÃ­denos fotografÃ­as, vÃ­deos o testimonios.',78,'es',63,NULL),('contact','contact','Introduce aquÃ­ tus datos de contacto.',79,'es',64,NULL),('collection_manage','newcollection','Para crear una nueva colecciÃ³n, introduce un nombre.',80,'es',65,NULL),('delete','introtext','Por favor, introduce tu contraseÃ±a para confirmar que quieres borrar este contenido.',81,'es',66,NULL),('done','deleted','El contenido ha sido borrado.',82,'es',67,NULL),('done','research_request','Un miembro del equipo del SERGI se asignarÃ¡ a tu peticiÃ³n. Te mantendremos informado durante el proceso. Una vez tengamos los resultados de tu peticiÃ³n te enviaremos un correo electrÃ³nico con un enlace a los contenidos seleccionados.',83,'es',68,NULL),('all','searchpanel','Busca por descripciones, palabras clave y cÃ³digos de contenido.',84,'es',69,NULL),('about','about','ImÃ¡genes y Palabras pretende ser un lugar...',85,'es',70,NULL),('collection_edit','introtext','Organiza tu trabajo agrupando recursos. Crea tantas colecciones como necesites.\r\n<br>\r\nTodas la colecciones aparecerÃ¡n en tu panel \"Mis colecciones\" en la parte inferior de la pantalla.\r\n<br><br>\r\n<strong>Acceso privado</strong> sÃ³lo permite a ti y a los usuarios que selecciones a visualizar la colecciÃ³n. <br><br>\r\n<strong>Accesos pÃºblico</strong> permite a todos los usuarios de la aplicaciÃ³n a visualizar la colecciÃ³n.\r\n<br><br>\r\nTambiÃ©n puedes elegir quÃ© usuarios podrÃ¡n modificar la colecciÃ³n o simplemente podrÃ¡n visualizarla.',86,'es',71,NULL),('terms and conditions','terms and conditions','Excepto en el caso de que existan condiciones especiales para el uso del material, IntermÃ³n Oxfam y Oxfam Internacional estÃ¡n autorizados por los autores a utilizar las fotografÃ­as, vÃ­deos y testimonios de esta aplicaciÃ³n para cualquier uso vinculado a la misiÃ³n y los objetivos de IntermÃ³n Oxfam y Oxfam Internacional.\r\n\r\nTÃ©rminos y condiciones de uso\r\n\r\nCada vez que te descargues una fotografÃ­a, vÃ­deo o testimonio de esta aplicaciÃ³n debes cumplir con los siguientes tÃ©rminos y condiciones:\r\n\r\n1. Todas las fotografÃ­as, vÃ­deos y testimonios deben ir acompaÃ±ados de una referencia como mÃ­nimo al trabajo de IntermÃ³n Oxfam u Oxfam Internacional. \r\n\r\n2. El equipo de IntermÃ³n Oxfam NO estÃ¡ autorizado a vender ninguna de las fotografÃ­as, vÃ­deos o testimonios de esta aplicaciÃ³n (sin previa autorizaciÃ³n del SERGI). \r\n\r\n3. Todas las fotografÃ­as deben ir acompaÃ±adas de su respectivo copyright. Ejemplo: (c) Sara Bahuer/IntermÃ³n Oxfam.\r\n\r\n4. Todos los  vÃ­deos y testimonios deben ir firmados por el autor e IntermÃ³n Oxfam.\r\n\r\nCuando el autor del material haya impuesto restricciones adicionales al uso del mismo, se indicarÃ¡ en la descripciÃ³n que lo acompaÃ±a.\r\n\r\nSi estÃ¡s de acuerdo con estos tÃ©rminos y condiciones de uso, por favor, haz click en el boton de Aceptar y continÃºa con la descarga del material. \r\n',87,'es',72,NULL),('collection_email','introtext','Completa el formulario adjunto para enviar por mail esta colecciÃ³n. RecibirÃ¡s un link con todo el material y podrÃ¡s elegir lo que mÃ¡s se adapte a tus necesidades.',88,'es',76,NULL),('collection_manage','findpublic','Las colecciones pÃºblicas son grupos de material disponible para los usuarios del sistema. Para encontrarlas debes introducir un identificador. Ã‰ste puede ser el nombre completo o parcial de la colecciÃ³n que te interese o bien el ID de usuario de la misma. AÃ±Ã¡delo a tu lista de colecciones para acceder al material.\r\n\r\n',89,'es',77,NULL),('home','restrictedtitle','<h1>Bienvenido al Panel de Control</h1>',90,'es',78,NULL),('home','themes','AquÃ­ encontrarÃ¡s los materiales recogidos recientemente.',91,'es',79,NULL),('home','welcometext','',92,'es',80,NULL),('help','introtext','ObtÃ©n el mÃ¡ximo rendimiento del Espacio de Material. Estas guÃ­as te ayudarÃ¡n a usar el sistema con mayor efectividad.',93,'es',81,NULL),('home','welcometitle','Bienvenida/o a ImÃ¡genes y Palabras, una herramienta que te permitirÃ¡ buscar fotografÃ­as, vÃ­deos y testimonios entre sus mÃ¡s de xxx materiales.',94,'es',82,NULL),('team_batch','introtext','Puedes subir varios archivos al mismo tiempo colgÃ¡ndolos en un servidor FTP vÃ¡lido. Una vez hayas terminado, los archivos se pueden borrar de este servidor.',95,'es',83,NULL),('team_home','introtext','Bienvenido al Panel de Control. Utiliza los enlaces siguientes para gestionar materiales, responder a peticiones de materiales, gestionar temas o modificar configuraciones del sistema.',96,'es',84,NULL),('user_password','introtext','Introduce tu direcciÃ³n de e-mail y te enviaremos tu nombre de usuario y contraseÃ±a.',97,'es',85,NULL),('team_user','introtext','Utiliza esta secciÃ³n para aÃ±adir, eliminar o modificar usuarios.',98,'es',86,NULL),('collection_public','introtext','Encuentra una colecciÃ³n pÃºblica',99,'es',87,NULL),('home','mycollections','Esta herramienta te permite seleccionar, organizar y compartir tu material. ',100,'es',88,NULL),('collection_manage','introtext','Organiza y controla tu trabajo agrupando material. Crea \"Colecciones\" que se ajusten a tus necesidades de trabajo. PodrÃ­as querer agrupar colecciones en proyectos con los que trabajas independientemente, compartirlas con un equipo o simplemente guardar tu material favorito en un sitio concreto. Todas estas opciones aparecerÃ¡n en la pestaÃ±a \"Mis colecciones\" en un botÃ³n de tu pantalla.',101,'es',89,NULL),('done','collection_email','Los usuarios que quieras pueden recibir un correo con un enlace a la colecciÃ³n que les envÃ­es. Esta recopilaciÃ³n se aÃ±ade a su \"Lista de Colecciones\"',102,'es',90,NULL),('done','resource_request','Tu peticiÃ³n ha sido enviada y en breve nos pondremos en contacto contigo',103,'es',91,NULL),('done','user_password','Te hemos envÃ­ado un correo electrÃ³nico con tu ID de usuario y tu contraseÃ±a',104,'es',92,NULL),('done','user_request','Tu solicitud para obtener una cuenta de usuario ha sido enviada. Muy pronto te enviaremos los detalles.',105,'es',93,NULL),('download_click','introtext','Para bajar el archivo adjunto pincha en el enlace de abajo y elige \"Guardar como\". Te preguntarÃ¡n dÃ³nde quieres guardar el archivo. Para abrir el archivo en tu navegador sÃ³lo tienes que pinchar el enlace.',106,'es',94,NULL),('download_progress','introtext','La descarga comenzarÃ¡ en pocos segundos',107,'es',95,NULL),('edit','batch','Por favor, especifica el datos comunes a todas las imÃ¡genes que vas a cargar en lote.<br><br>\r\n<b>AtenciÃ³n:</b> El proceso de carga de imÃ¡genes por lote requiere muchos recursos de CPU. Por este motivo es aconsejable no hacer cargas simultÃ¡neas.',108,'es',96,NULL),('edit','multiple','Selecciona los campos que desees editar de la lista de abajo. Los que selecciones se sobreescribirÃ¡n sobre el material que estÃ©s editando. Cualquier campo no selecciÃ³nado serÃ¡ ignorado.',109,'es',97,NULL),('home','help','',110,'es',98,NULL),('home','restrictedtext','Por favor, pincha en el enlace que te envÃ­amos a tu correo para acceder a los materiales seleccionados para ti',111,'es',99,NULL),('login','welcomelogin','Bienvenido a Palabras y FotografÃ­as',112,'es',100,NULL),('research_request','introtext','Podemos ayudarte a encontrar el material que mejor se ajuste a tus necesidades. Completa el cuestionario tan minuciosamente como sea posible para que evaluemos los criterios que mÃ¡s te interesan. Estaremos en contacto por correo electrÃ³nico sobre la evoluciÃ³n del proceso y una vez hayamos completado la busqueda recibirÃ¡s un mail con un enlace al material que te recomendamos',113,'es',101,NULL),('resource_email','introtext','Comparte este material rÃ¡pidamente vÃ­a mail. Se enviarÃ¡ automÃ¡ticamente un enlace. AdemÃ¡s puedes incluir texto informativo como parte del correo electrÃ³nico.',114,'es',102,NULL),('resource_request','introtext','El material que has solicitado no esta disponible en Internet. La informaciÃ³n que has requerido se incluirÃ¡ automÃ¡ticamente en el correo electrÃ³nico, si quieres puedes aÃ±adir cualquier comentario adicional.',115,'es',103,NULL),('search_advanced','introtext','BÃºsqueda por Campos. Cualquier secciÃ³n que dejes en blanco o no selecciones, incluirÃ¡ todos esos tÃ©rminos en la bÃºsqueda. Por ejemplo, si dejas todas las opciones de un paÃ­s vacÃ­as, la bÃºsqueda te darÃ¡ resultados de todos esos paÃ­ses. Si seleccionas sÃ³lo Ãfrica entonces los resultados sÃ³lo contendrÃ¡n material de Ãfrica.\r\n',116,'es',104,NULL),('team_archive','introtext','Para editar materiales de archivo individualmente, simplemente busca por el material y pincha en \"Herramienta de Material\" en la misma pantalla. Todos los materiales listos para ser archivados serÃ¡n listados en Material Pendiente de Lista. Desde esta lista es posible aÃ±adir informaciÃ³n posteriormente y transferir el material grabado al archivo\r\n',117,'es',105,NULL),('view','storyextract','Estracto de historia:',118,'es',106,NULL),('user_request','introtext','Por favor completa el formulario adjunto para solicitar una cuenta de usuario.',119,'es',107,NULL),('team_research','introtext','Organiza y controla â€œCriterios de BÃºsquedaâ€. Elige â€œEditar bÃºsquedaâ€ para examinar los detalles de la bÃºsqueda y asignarle la investigaciÃ³n a un miembro del equipo. Es posible basar una bÃºsqueda en una colecciÃ³n previa introduciendo el nombre de la misma en â€œEditâ€. Una vez sean asignados los criterios de bÃºsqueda, selecciona â€œEditar ColecciÃ³nâ€ para aÃ±adir los criterios al panel â€œMi colecciÃ³nâ€. Usando las herramientas estÃ¡ndar es posible aÃ±adir material a la bÃºsqueda. Una vez se ha completado, selecciona â€œEditar bÃºsquedaâ€ y cambia el estado para recibir automÃ¡ticamente un correo electrÃ³nico con la informaciÃ³n solicitada. El mail contendrÃ¡ un enlace a la bÃºsqueda y ademÃ¡s se aÃ±adirÃ¡ automÃ¡ticamente al panel â€œMi colecciÃ³nâ€.',120,'es',108,NULL),('team_batch_select','introtext','Puedes subir varios archivos al mismo tiempo colgÃ¡ndolos en un servidor FTP vÃ¡lido. Una vez hayas terminado, los archivos se pueden borrar de este servidor.',121,'es',109,NULL),('team_batch_upload','introtext','Puedes subir varios archivos al mismo tiempo colgÃ¡ndolos en un servidor FTP vÃ¡lido. Una vez hayas terminado, los archivos se pueden borrar de este servidor.',122,'es',110,NULL),('team_resource','introtext','AÃ±ade material individual o sube varios materiales simultÃ¡neamente. Para editar materiales individuales sÃ³lo tienes que buscar el material, y pinchar en editar en \"Herramienta de Material\" de la pantalla de material.',123,'es',111,NULL),('team_stats','introtext','Los grÃ¡ficos son generados a bajo demanda. Marca la casilla para imprimir todos los grÃ¡ficos del aÃ±os seleccionado.',124,'es',112,NULL),('terms','introtext','Antes de iniciar el proceso de descarga debes aceptar los siguientes tÃ©rminos y condiciones de uso.\r\n',125,'es',113,NULL),('terms','terms','Excepto en el caso de que existan condiciones especiales para el uso del material, IntermÃ³n Oxfam y Oxfam Internacional estÃ¡n autorizados por los autores a utilizar las fotografÃ­as, vÃ­deos y testimonios de esta aplicaciÃ³n para cualquier uso vinculado a la misiÃ³n y los objetivos de IntermÃ³n Oxfam y Oxfam Internacional.\r\n\r\nTÃ©rminos y condiciones de uso\r\n\r\nCada vez que te descargues una fotografÃ­a, vÃ­deo o testimonio de esta aplicaciÃ³n debes cumplir con los siguientes tÃ©rminos y condiciones:\r\n\r\n1. Todas las fotografÃ­as, vÃ­deos y testimonios deben ir acompaÃ±ados de una referencia como mÃ­nimo al trabajo de IntermÃ³n Oxfam u Oxfam Internacional. \r\n\r\n2. El equipo de IntermÃ³n Oxfam NO estÃ¡ autorizado a vender ninguna de las fotografÃ­as, vÃ­deos o testimonios de esta aplicaciÃ³n (sin previa autorizaciÃ³n del SERGI). \r\n\r\n3. Todas las fotografÃ­as deben ir acompaÃ±adas de su respectivo copyright. Ejemplo: (c) Sara Bahuer/IntermÃ³n Oxfam.\r\n\r\n4. Todos los  vÃ­deos y testimonios deben ir firmados por el autor e IntermÃ³n Oxfam.\r\n\r\nCuando el autor del material haya impuesto restricciones adicionales al uso del mismo, se indicarÃ¡ en la descripciÃ³n que lo acompaÃ±a.\r\n\r\nSi estÃ¡s de acuerdo con estos tÃ©rminos y condiciones de uso, por favor, haz click en el boton de Aceptar y continÃºa con la descarga del material. \r\n',126,'es',114,NULL),('themes','findpublic','Encuentra una colecciÃ³n pÃºblica introduciendo un tÃ©rmino de bÃºsqueda',127,'es',115,NULL),('upload','introtext','Sube un archivo empleando el formulario adjunto',128,'es',116,NULL),('themes','manage','Organiza y edita los temas disponibles en la red. Los temas aparecen organizados en colecciones. Para crear una nueva entrada e incluirla en un tema, debes pinchar en \"Construye una colecciÃ³n\" y elegir \"Mis Colecciones\" del menÃº principal. Recuerda incluir el nombre de un tema durante la configuraciÃ³n y crear una nueva colecciÃ³n. Puedes usar un nombre ya existente para agrupar la nueva colecciÃ³n en un apartado ya establecido (es importante asegurarse de que el nombre es exactamente el mismo) o elegir un nuevo tÃ­tulo para crear una nueva categorÃ­a de temas. Nunca permitas a los usuarios que aÃ±adan o cambien materiales de las colecciones ya establecidas. Para editar el contenido de una entrada existente bajo un tema, elige â€œEditar una ColecciÃ³nâ€. Los iconos de esta colecciÃ³n aparecerÃ¡n en el botÃ³n de la pantalla â€œMis Coleccionesâ€. Emplea las herramientas estÃ¡ndar para editar, mover o aÃ±adir material. Para cambiar el  nombre de un tema o mover una colecciÃ³n y que Ã©sta aparezca ubicada en un tema diferente. Elige: â€œEditar Propiedadesâ€ y edita la categorÃ­a del tema o el nombre de la colecciÃ³n. Selecciona un nombre existente para el tema y asÃ­ agruparlo en de las categorÃ­as existentes (asegÃºrate de que lo escribes exactamente igual), o elige un nuevo tÃ­tulo para crear un nuevo tipo de tema. Para borrar una selecciÃ³n del tema. Elige: â€œEditar Propiedadesâ€ y borra las palabras de la caja de categorÃ­as del tema.',129,'es',117,NULL),('themes','introtext','Los temas estÃ¡n formados por grupos con nuestros mejores materiales',130,'es',118,NULL),('team_copy','introtext','Introduce el identificador del material que te gustarÃ­a copiar. SÃ³lo se copiarÃ¡ el material seÃ±alado -no se copiarÃ¡ ningÃºn archivo adjunto.',131,'es',119,NULL),('change_language','introtext','Por favor, selecciona el idioma en el que deseas trabajar.',132,'es',123,NULL),('team_content','introtext','',133,'es',120,NULL),('team_report','introtext','Por favor, elige un informe y un rango de fecha. El informe podrÃ¡ abrirse en un documento de Excel o un documento de caracterÃ­sticas similares.',134,'es',121,NULL),('change_password','introtext','Por favor introduzca una nueva contraseÃ±a.',135,'es',125,NULL),('home','welcometitle','Welcome to ResourceSpace, Exxon Staff',144,'en',NULL,7),('home','welcometitle','Welcome to ResourceSpace, Exxon Admin',143,'en',NULL,6),('upload_fancy','introtext','Upload your files below. You can select multiple files when browsing by holding down the \'shift\' key.',145,'en',NULL,NULL),('tag','introtext','Help to improve search results by tagging resources. Say what you see, separated by spaces or commas... for example: dog, house, ball, birthday cake. Enter the full name of anyone visible in the photo and the location the photo was taken if known.',146,'en',NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `site_text` ENABLE KEYS */;

--
-- Table structure for table `sysvars`
--

DROP TABLE IF EXISTS `sysvars`;
CREATE TABLE `sysvars` (
  `name` varchar(50) default NULL,
  `value` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sysvars`
--


/*!40000 ALTER TABLE `sysvars` DISABLE KEYS */;
LOCK TABLES `sysvars` WRITE;
INSERT INTO `sysvars` VALUES ('lastsync','2001-01-01');
UNLOCK TABLES;
/*!40000 ALTER TABLE `sysvars` ENABLE KEYS */;


--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `ref` int(11) NOT NULL auto_increment,
  `username` varchar(50) character set latin1 default NULL,
  `password` varchar(50) character set latin1 default NULL,
  `fullname` varchar(100) character set latin1 default NULL,
  `email` varchar(100) character set latin1 default NULL,
  `usergroup` int(11) default NULL,
  `last_active` datetime default NULL,
  `logged_in` int(11) default NULL,
  `last_browser` varchar(100) character set latin1 default NULL,
  `last_ip` varchar(100) character set latin1 default NULL,
  `current_collection` int(11) default NULL,
  `accepted_terms` int(11) NOT NULL default '0',
  `account_expires` datetime default NULL,
  `comments` text character set latin1,
  PRIMARY KEY  (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--


/*!40000 ALTER TABLE `user` DISABLE KEYS */;
LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'admin','admin','Admin User','admin@nowhere.null',3,'2007-11-15 20:30:10',0,'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/522.11 (KHTML, like Gecko) Version/3.0.2','127.0.0.1',76,1,NULL,'');
UNLOCK TABLES;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

--
-- Table structure for table `user_collection`
--

DROP TABLE IF EXISTS `user_collection`;
CREATE TABLE `user_collection` (
  `user` int(11) default NULL,
  `collection` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_collection`
--


/*!40000 ALTER TABLE `user_collection` DISABLE KEYS */;
LOCK TABLES `user_collection` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `user_collection` ENABLE KEYS */;

--
-- Table structure for table `usergroup`
--

DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE `usergroup` (
  `ref` int(11) NOT NULL auto_increment,
  `name` varchar(50) character set latin1 default NULL,
  `permissions` text character set latin1,
  `fixed_theme` varchar(50) default NULL,
  `parent` int(11) default NULL,
  `search_filter` text,
  PRIMARY KEY  (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usergroup`
--


/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
LOCK TABLES `usergroup` WRITE;
INSERT INTO `usergroup` VALUES (1,'Administrators','s,c,e,t,h,r,u,i,e-2,e-1,e0,e1,v,o,m,d,q,n,f*,j*','',0,''),(2,'General Users','s,e-1,e-2,g,d,q,n,f*,j*','',0,''),(3,'Super Admin','s,c,e,a,t,h,u,r,i,e-2,e-1,e0,e1,e2,o,m,g,v,d,q,n,f*,j*','',0,''),(4,'Archivists','s,c,e,t,h,r,u,i,e1,e2,v,q,n,f*,j*','',0,''),(5,'Restricted User',NULL,'',NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;






-- --------------------------------------------------------

--
-- Table structure for table `opportunity_history`
--

CREATE TABLE `opportunities_history` (
  `opp_id` bigint(20) default NULL,
   `assigned_to` int,
   `loggedin_user` varchar(32),
  `deal_status` varchar(32) default NULL,
 
  `date_changed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `notes` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;





SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dealtrackeruser`
--


-- --------------------------------------------------------

--
-- Table structure for table `linkbuilders`
--

CREATE TABLE `linkbuilders` (
  `linkbuilder_id` int(11) NOT NULL auto_increment,
  `alias_name` varchar(64) default NULL,
  `alias_email` varchar(128) default NULL,
  `name_first` varchar(32) default NULL,
  `name_last` varchar(32) default NULL,
  `email` varchar(128) default NULL,
  `phone` varchar(64) default NULL,
  `status` varchar(32) default NULL,
  `signature` text default NULL,
  PRIMARY KEY  (`linkbuilder_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;


-- --------------------------------------------------------

--
-- Table structure for table `mail_queue`
--

CREATE TABLE `mail_queue` (
  `message_id` bigint(20) NOT NULL auto_increment,
  `opp_id` bigint(20) default NULL,
   message_subject varchar(128),
    message_to varchar(128),
  `message_body` text,
  `linkbuilder_id` int(11) default NULL,
  sent_status int,
  `date_queued` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `mail_templates`
--

CREATE TABLE `mail_templates` (
  `template_id` bigint(20) NOT NULL auto_increment,
  `linkbuilder_id` int(11) default NULL,
  `template_title` varchar(40) default NULL,
  `message_subject` varchar(128) default NULL,
  `message_body` text,
  `actions_status` varchar(32) default NULL,
  `last_edit` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


-- --------------------------------------------------------

--
-- Table structure for table `mail_log`
--

CREATE TABLE `mail_log` (
  `event_id` bigint(20) NOT NULL auto_increment,
  `message_id` bigint(20) default NULL,
  `entry` text,
  `event_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `opportunities`
--

CREATE TABLE `opportunities` (
  `opp_id` bigint(20) NOT NULL auto_increment,
  `domain_name` varchar(128) default NULL,
  `page_rank` int(11) default NULL,
  `link_target` varchar(128) default NULL,
  `deal_status` varchar(32) default NULL,
  `notes` varchar(512) default NULL,
  `contact_id` int(11) default NULL,
  `posting_date` varchar(16) default NULL,
  `linkbuilder_id` int(11) default NULL,
  `group_id` int(11) default NULL,
  `date_verified` varchar(16) default NULL,
  `last_action` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `special_codes` varchar(255) default NULL,
  `deal_cost` varchar(10) default NULL,
  `breakdown_doc` varchar(10) default NULL,
  `duration` int(11) default NULL,
  `components` int(11) default NULL,
  `deal_value` int(11) default NULL,
  `keywords` varchar(128) default NULL,
  `deal_date` varchar(16) default NULL,
  `posting_fee` varchar(10) default NULL,
  `finder` varchar(64) default NULL,
  `approved` varchar(10) default NULL,
  `keyword_targets` varchar(128) default NULL,
  `article_location` varchar(512) default NULL,
  `paypal_email` varchar(64) default NULL,
  `actions_status` varchar(32) default NULL,
  `social_location` varchar(512) default NULL,
  `closing_task` varchar(40) default NULL,
  PRIMARY KEY  (`opp_id`),
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1242 ;


-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL auto_increment,
  `name_first` varchar(32) default NULL,
  `name_last` varchar(32) default NULL,
  `email` varchar(128) default NULL,
  `phone` varchar(64) default NULL,
  `status` varchar(32) default NULL,
  `notes` text,
  PRIMARY KEY  (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(64) default NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

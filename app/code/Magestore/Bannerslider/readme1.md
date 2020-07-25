如果没创建表 手动创建 执行以下sql
CREATE TABLE `magestore_bannerslider_banner` (
  `banner_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Banner ID',
  `name` tinytext CHARACTER SET latin1 NOT NULL COMMENT 'Banner name',
  `order_banner` int(10) DEFAULT '0' COMMENT 'Banner order',
  `slider_id` int(10) DEFAULT NULL COMMENT 'Slider Id',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Banner status',
  `click_url` text CHARACTER SET latin1 COMMENT 'Banner click url',
  `imptotal` int(10) DEFAULT '0' COMMENT 'Banner imptotal',
  `clicktotal` int(10) DEFAULT '0' COMMENT 'Banner click total',
  `target` int(10) DEFAULT '0' COMMENT 'Banner target',
  `image` text CHARACTER SET latin1 COMMENT 'Banner image',
  `image_alt` tinytext CHARACTER SET latin1 COMMENT 'Banner image alt',
  `style_slide` tinytext CHARACTER SET latin1 COMMENT 'Banner style',
  `width` float DEFAULT NULL COMMENT 'Banner width',
  `height` float DEFAULT NULL COMMENT 'Banner height',
  `start_time` datetime DEFAULT NULL COMMENT 'Banner starting time',
  `end_time` datetime DEFAULT NULL COMMENT 'Banner ending time',
  `caption` text CHARACTER SET latin1 COMMENT 'Banner caption',
  PRIMARY KEY (`banner_id`),
  KEY `slider_id` (`slider_id`,`status`,`start_time`,`end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8


CREATE TABLE `magestore_bannerslider_report` (
  `report_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Report ID',
  `banner_id` int(10) NOT NULL DEFAULT '0' COMMENT 'Banner ID',
  `slider_id` int(10) NOT NULL DEFAULT '0' COMMENT 'Slider ID',
  `impmode` int(10) NOT NULL DEFAULT '0' COMMENT 'Impressions mode',
  `clicks` int(10) NOT NULL DEFAULT '0' COMMENT 'Banner Clicks',
  `date_click` datetime DEFAULT NULL COMMENT 'Banner click date time',
  PRIMARY KEY (`report_id`),
  KEY `banner_id` (`banner_id`,`slider_id`,`impmode`,`clicks`,`date_click`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8


CREATE TABLE `magestore_bannerslider_slider` (
  `slider_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Slider ID',
  `title` text CHARACTER SET latin1 NOT NULL COMMENT 'Slider title',
  `position` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Slider position',
  `show_title` smallint(6) DEFAULT '1' COMMENT 'Show Title',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Slider status',
  `sort_type` int(10) DEFAULT NULL COMMENT 'Sort type',
  `description` text CHARACTER SET latin1 COMMENT 'Slider description',
  `category_ids` text CHARACTER SET latin1 COMMENT 'Slider category ids',
  `style_content` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Slider style content',
  `custom_code` text CHARACTER SET latin1 COMMENT 'Slider custom code',
  `style_slide` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Slider style',
  `width` float DEFAULT NULL COMMENT 'Slider width',
  `height` float DEFAULT NULL COMMENT 'Slider height',
  `note_color` text CHARACTER SET latin1 COMMENT 'Slider note color',
  `animationB` text CHARACTER SET latin1 COMMENT 'Slider animationB',
  `caption` smallint(6) DEFAULT NULL COMMENT 'Slider caption',
  `position_note` smallint(6) DEFAULT '1' COMMENT 'Slider position note',
  `slider_speed` float DEFAULT NULL COMMENT 'Slider speed',
  `url_view` text CHARACTER SET latin1 COMMENT 'Slider url view',
  `min_item` smallint(6) DEFAULT NULL COMMENT 'Slider min item',
  `max_item` smallint(6) DEFAULT NULL COMMENT 'Slider max item',
  PRIMARY KEY (`slider_id`),
  KEY `position` (`position`,`status`,`style_content`,`style_slide`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8


CREATE TABLE `magestore_bannerslider_value` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `banner_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Banner ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store view ID',
  `attribute_code` varchar(63) CHARACTER SET latin1 NOT NULL DEFAULT '' COMMENT 'Attribute code',
  `value` text CHARACTER SET latin1 NOT NULL COMMENT 'Value',
  PRIMARY KEY (`value_id`),
  KEY `banner_id` (`banner_id`,`store_id`,`attribute_code`),
  KEY `r_value` (`store_id`),
  CONSTRAINT `r_banner` FOREIGN KEY (`banner_id`) REFERENCES `magestore_bannerslider_banner` (`banner_id`),
  CONSTRAINT `r_value` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

